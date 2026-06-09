<?php

namespace App\Services\Customer;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Book;

class CartService
{
    /**
     * Get or create cart for customer.
     */
    public function getOrCreateCart(int $customerId): Cart
    {
        return Cart::firstOrCreate(['customer_id' => $customerId]);
    }

    /**
     * Add item to cart.
     */
    public function addItem(int $customerId, int $bookId, int $quantity): void
    {
        $cart = $this->getOrCreateCart($customerId);
        $book = Book::findOrFail($bookId);

        // Check stock
        if ($quantity > $book->stock_quantity) {
            throw new \Exception('Not enough stock available.');
        }

        // Check if already in cart — update quantity
        $existingItem = $cart->items()->where('book_id', $bookId)->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $quantity;
            if ($newQty > $book->stock_quantity) {
                throw new \Exception('Cannot exceed available stock.');
            }
            $existingItem->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'book_id'  => $bookId,
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(int $cartItemId, int $quantity): void
    {
        $item = CartItem::findOrFail($cartItemId);
        $book = $item->book;

        if ($quantity > $book->stock_quantity) {
            throw new \Exception('Cannot exceed available stock.');
        }

        $item->update(['quantity' => $quantity]);
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $cartItemId): void
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->delete();
    }

    /**
     * Get cart with items for JSON response.
     */
    public function getCart(int $customerId): array
    {
        $cart = Cart::with('items.book')->where('customer_id', $customerId)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return [
                'items'       => [],
                'total'       => 0,
                'total_items' => 0,
            ];
        }

        $items = $cart->items->map(function ($item) {
            return [
                'id'       => $item->id,
                'title'    => $item->book->title,
                'price'    => $item->book->isOnSale() ? $item->book->sale_price : $item->book->price,
                'quantity' => $item->quantity,
                'image'    => $item->book->image ? asset('storage/' . $item->book->image) : 'https://placehold.co/70x95/e2e8f0/64748b?text=Book',
            ];
        });

        return [
            'items'       => $items,
            'total'       => $cart->items->sum(function($i) {
                $price = $i->book->isOnSale() ? $i->book->sale_price : $i->book->price;
                return $price * $i->quantity;
            }),
            'total_items' => $cart->items->sum('quantity'),
        ];
    }
}