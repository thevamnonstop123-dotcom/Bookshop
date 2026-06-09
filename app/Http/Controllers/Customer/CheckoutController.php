<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CheckoutService;
use Illuminate\Http\Request;

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

        $total = $cart->items->sum(function($item) {
            $price = $item->book->isOnSale() ? $item->book->sale_price : $item->book->price;
            return $price * $item->quantity;
        });

        return view('customer.checkout', compact('cart', 'addresses', 'total'));
    }

    /**
     * Process checkout and redirect to Stripe.
     */
        /**
     * Process checkout and redirect to Stripe.
     */
        /**
     * Process checkout.
     */
    public function process(Request $request)
    {
        $customerId = auth('customer')->id();
        $paymentMethod = $request->payment_method ?? 'stripe';

        // Use existing address or create new one
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

        try {
            // For Stripe — redirect to Stripe checkout
            if ($paymentMethod === 'stripe') {
                $session = $this->checkoutService->createStripeSession($customerId, $addressId);
                return redirect($session->url);
            }

            // For KPay, Wave, COD — process directly
            $order = $this->checkoutService->processDirectPayment($customerId, $addressId, $paymentMethod);

            return view('customer.checkout-success', compact('order'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    /**
     * Handle successful payment.
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('customer.home');
        }

        try {
            $order = $this->checkoutService->processSuccessfulPayment($sessionId);

            return view('customer.checkout-success', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('customer.home')
                ->with('error', 'Something went wrong. Please contact support.');
        }
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel()
    {
        return view('customer.checkout-cancel');
    }
}