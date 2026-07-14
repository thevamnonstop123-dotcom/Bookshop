<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Show checkout page.
     */
    public function index()
    {
        $customer = auth('customer')->user();
        $cart = \App\Models\Cart::with('items.book')->where('customer_id', $customer->id)->first();
        $addresses = $customer->addresses()->latest()->get();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('books.index')
                ->with('error', 'Your cart is empty.');
        }

        $validItems = $cart->items->filter(function($item) {
            return $item->book !== null;
        });

        if ($validItems->isEmpty()) {
            return redirect()->route('books.index')
                ->with('error', 'Your cart is empty.');
        }

        $total = $validItems->sum(function($item) {
            $format = $item->format ?? 'physical';
            $price = $item->book->getPriceForFormat($format);
            return $price * $item->quantity;
        });

        $subtotal = $validItems->sum(function($item) {
            return $item->book->price * $item->quantity;
        });

        $savings = $subtotal - $total;

        return view('customer.checkout.index', compact('cart', 'addresses', 'total', 'subtotal', 'savings'));
    }

    /**
     * Process checkout with retry logic.
     */
    public function process(Request $request)
    {
        $customerId = auth('customer')->id();
        $paymentMethod = $request->payment_method ?? 'stripe';
        $maxRetries = 2;
        $attempt = 0;

        // Check if cart has physical items
        $cart = \App\Models\Cart::with('items')->where('customer_id', $customerId)->first();
        $hasPhysicalItems = $cart && $cart->items->contains(function($item) {
            return ($item->format ?? 'physical') === 'physical';
        });

        // Validate address only if there are physical items
        if ($hasPhysicalItems) {
            if ($request->address_id) {
                $request->validate(['address_id' => 'required|exists:customer_addresses,id']);
                $addressId = $request->address_id;
            } else {
                $request->validate([
                    'receiver_name' => 'required|string|max:100',
                    'phone_number'  => 'required|regex:/^09[0-9]{9}$/',
                    'address_line'  => 'required|string|max:500',
                ]);

                $address = \App\Models\CustomerAddress::create([
                    'customer_id'   => $customerId,
                    'receiver_name' => $request->receiver_name,
                    'phone_number'  => $request->phone_number,
                    'address_line'  => $request->address_line,
                    'is_default'    => true,
                ]);

                $addressId = $address->id;
            }
        } else {
            // Ebook only - no address needed, use a placeholder
            $addressId = null;
        }

        // Retry loop for payment failures
        while ($attempt <= $maxRetries) {
            try {
                // For Stripe — redirect to Stripe checkout
                if ($paymentMethod === 'stripe') {
                    $session = $this->checkoutService->createStripeSession($customerId, $addressId);
                    return redirect($session->url);
                }

                // For KPay, Wave, COD — process and REDIRECT to success page
                $order = $this->checkoutService->processDirectPayment($customerId, $addressId, $paymentMethod);

                // Store order ID in session and redirect
                session()->flash('order_id', $order->id);
                return redirect()->route('checkout.success');
                
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $attempt++;
                Log::warning('Stripe connection failed, attempt ' . $attempt);
                
                if ($attempt > $maxRetries) {
                    return back()->with('error', 'Payment service is temporarily unavailable. Please try again in a few minutes.');
                }
                sleep(1);
                
            } catch (\Stripe\Exception\CardException $e) {
                return back()->with('error', 'Your card was declined. Please try a different card.');
                
            } catch (\Stripe\Exception\RateLimitException $e) {
                return back()->with('error', 'Too many attempts. Please wait a moment and try again.');
                
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                Log::error('Stripe invalid request', ['error' => $e->getMessage()]);
                return back()->with('error', 'Payment configuration error. Please contact support.');
                
            } catch (\Exception $e) {
                Log::error('Checkout failed', [
                    'customer_id' => $customerId,
                    'error' => $e->getMessage()
                ]);
                
                return back()->with('error', 'An unexpected error occurred. Please try again.');
            }
        }
    }

    /**
     * Show success page (only with valid session data).
     */
    public function success()
    {
        // Get order ID from session flash data
        $orderId = session('order_id');
        
        if (!$orderId) {
            return redirect()->route('customer.home');
        }

        $order = \App\Models\Order::with('payment')->find($orderId);
        
        if (!$order) {
            return redirect()->route('customer.home');
        }

        return view('customer.checkout.success', compact('order'));
    }

    /**
     * Handle Stripe success callback.
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('customer.home');
        }

        try {
            $order = $this->checkoutService->processSuccessfulPayment($sessionId);
            session()->flash('order_id', $order->id);
            return redirect()->route('checkout.success');
        } catch (\Exception $e) {
            Log::error('Payment success handling failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('customer.home')
                ->with('error', 'Payment was processed but we encountered an issue. Please check your orders or contact support.');
        }
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel()
    {
        return view('customer.checkout.cancel');
    }
}
