<?php

namespace App\Services\Customer;

use App\Models\Cart;
use App\Models\Order;
use App\Services\NotificationService;
use App\Models\OrderItem;
use App\Models\OrderShippingAddress;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    /**
     * Get valid cart items (with existing books only).
     */
    private function getValidItems(Cart $cart)
    {
        return $cart->items->filter(function ($item) {
            return $item->book !== null;
        });
    }

    /**
     * Get the effective price of a book based on format.
     */
    private function getBookPrice($book, string $format = 'physical'): float
    {
        if (!$book) return 0;
        return $book->getPriceForFormat($format);
    }

    /**
     * Create a Stripe checkout session.
     */
    public function createStripeSession(int $customerId, ?int $addressId): Session
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $cart = Cart::with('items.book')->where('customer_id', $customerId)->first();
        if (!$cart) throw new \Exception('Your cart is empty.');

        $validItems = $this->getValidItems($cart);
        if ($validItems->isEmpty()) throw new \Exception('Your cart is empty.');

        $address = null;
        if ($addressId) {
            $address = \App\Models\CustomerAddress::where('id', $addressId)
                ->where('customer_id', $customerId)->first();
            if (!$address) throw new \Exception('Invalid shipping address.');
        }

        $lineItems = [];
        foreach ($validItems as $item) {
            $format = $item->format ?? 'physical';
            $price = $this->getBookPrice($item->book, $format);
            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'mmk',
                    'product_data' => ['name' => $item->book->title . ' (' . ($format === 'ebook' ? 'eBook' : 'Paperback') . ')'],
                    'unit_amount'  => (int)($price * 100),
                ],
                'quantity'   => $item->quantity,
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => $lineItems,
            'mode'                 => 'payment',
            'success_url'          => route('checkout.stripe-success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('checkout.cancel'),
            'metadata'             => [
                'customer_id' => (string) $customerId,
                'address_id'  => $addressId ? (string) $addressId : '0',
            ],
        ]);
    }

    /**
     * Process successful Stripe payment from callback.
     */
    public function processSuccessfulPayment(string $sessionId): Order
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::retrieve($sessionId);
        $metadata = $session->metadata;
        
        $customerId = (int) ($metadata->customer_id ?? 0);
        $addressId = ($metadata->address_id ?? null) ? (int) $metadata->address_id : null;

        if (!$customerId) {
            Log::error('Stripe callback missing customer_id', ['session_id' => $sessionId, 'metadata' => $metadata]);
            throw new \Exception('Invalid session data.');
        }

        $cart = Cart::with('items.book')->where('customer_id', $customerId)->first();
        
        if (!$cart || $cart->items->isEmpty()) {
            $existingPayment = Payment::where('transaction_reference', $session->payment_intent)->first();
            if ($existingPayment) {
                return $existingPayment->order;
            }
            throw new \Exception('Cart is empty and no existing order found.');
        }

        $address = null;
        if ($addressId) {
            $address = \App\Models\CustomerAddress::find($addressId);
            if (!$address) {
                $address = \App\Models\CustomerAddress::where('customer_id', $customerId)->first();
            }
        }

        $validItems = $this->getValidItems($cart);
        $totalAmount = $validItems->sum(fn($item) => $this->getBookPrice($item->book, $item->format ?? 'physical') * $item->quantity);

        $order = Order::create([
            'customer_id'  => $customerId,
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => $totalAmount,
            'status'       => 'pending',
        ]);

        foreach ($validItems as $cartItem) {
            $format = $cartItem->format ?? 'physical';
            $price = $this->getBookPrice($cartItem->book, $format);
            OrderItem::create([
                'order_id' => $order->id,
                'book_id'  => $cartItem->book_id,
                'quantity' => $cartItem->quantity,
                'price'    => $price,
                'format'   => $format,
            ]);
            if ($format === 'physical') {
                $cartItem->book->decrement('stock_quantity', $cartItem->quantity);
                $cartItem->book->refresh();
                $this->autoSetAvailability($cartItem->book);
                $this->checkStockAlert($cartItem->book);
            }
        }

        if ($address) {
            OrderShippingAddress::create([
                'order_id'      => $order->id,
                'receiver_name' => $address->receiver_name,
                'phone_number'  => $address->phone_number,
                'address_line'  => $address->address_line,
            ]);
        }

        Payment::create([
            'order_id'              => $order->id,
            'payment_method'        => 'stripe',
            'transaction_reference' => $session->payment_intent,
            'amount'                => $totalAmount,
            'status'                => 'completed',
            'paid_at'               => now(),
        ]);

        $cart->items()->delete();
        
        NotificationService::send(NotificationService::orderRoles(), "new_order", "New Order #" . $order->order_number, $order->customer->name . " placed an order for " . number_format($order->total_amount) . " MMK", $order);
        
        return $order;
    }

    /**
     * Process direct payment (KPay, Wave, COD).
     */
    public function processDirectPayment(int $customerId, ?int $addressId, string $paymentMethod): Order
    {
        $cart = Cart::with('items.book')->where('customer_id', $customerId)->first();
        $address = $addressId ? \App\Models\CustomerAddress::find($addressId) : null;
        if (!$cart) throw new \Exception('Cart is empty.');

        $validItems = $this->getValidItems($cart);
        $totalAmount = $validItems->sum(fn($item) => $this->getBookPrice($item->book, $item->format ?? 'physical') * $item->quantity);

        $order = Order::create([
            'customer_id'  => $customerId,
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => $totalAmount,
            'status'       => 'pending',
        ]);

        foreach ($validItems as $cartItem) {
            $format = $cartItem->format ?? 'physical';
            $price = $this->getBookPrice($cartItem->book, $format);
            OrderItem::create([
                'order_id' => $order->id,
                'book_id'  => $cartItem->book_id,
                'quantity' => $cartItem->quantity,
                'price'    => $price,
                'format'   => $format,
            ]);
            if ($format === 'physical') {
                $cartItem->book->decrement('stock_quantity', $cartItem->quantity);
                $cartItem->book->refresh();
                $this->autoSetAvailability($cartItem->book);
                $this->checkStockAlert($cartItem->book);
            }
        }

        if ($address) {
            OrderShippingAddress::create([
                'order_id'      => $order->id,
                'receiver_name' => $address->receiver_name,
                'phone_number'  => $address->phone_number,
                'address_line'  => $address->address_line,
            ]);
        }

        Payment::create([
            'order_id'       => $order->id,
            'payment_method' => $paymentMethod,
            'amount'         => $totalAmount,
            'status'         => $paymentMethod === 'cod' ? 'pending' : 'completed',
            'paid_at'        => $paymentMethod === 'cod' ? null : now(),
        ]);

        $cart->items()->delete();
        NotificationService::send(NotificationService::orderRoles(), "new_order", "New Order #" . $order->order_number, $order->customer->name . " placed an order for " . number_format($order->total_amount) . " MMK", $order);
        return $order;
    }

    private function autoSetAvailability($book): void
    {
        if ($book->stock_quantity <= 0) {
            $book->updateQuietly(["availability_status" => "out_of_stock"]);
        } elseif ($book->stock_quantity <= 5) {
            $book->updateQuietly(["availability_status" => "low_stock"]);
        }
    }

    private function checkStockAlert($book): void
    {
        if ($book->stock_quantity <= 0) {
            NotificationService::send(NotificationService::bookRoles(), "out_of_stock", "\"{$book->title}\" is out of stock", "Stock reached 0. Please restock.", $book);
        } elseif ($book->stock_quantity <= 5) {
            NotificationService::send(NotificationService::bookRoles(), "low_stock", "\"{$book->title}\" is low in stock", "Only {$book->stock_quantity} left.", $book);
        }
    }
}
