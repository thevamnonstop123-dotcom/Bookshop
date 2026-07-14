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
    public function addItem(int $customerId, int $bookId, int $quantity, string $format = 'physical'): void
    {
        $cart = $this->getOrCreateCart($customerId);
        $book = Book::findOrFail($bookId);

        // Validate format
        $availableFormats = $book->getAvailableFormats();
        if (!in_array($format, $availableFormats)) {
            throw new \Exception('Selected format is not available for this book.');
        }

        // Only check stock for physical format
        if ($format === 'physical' && $quantity > $book->stock_quantity) {
            throw new \Exception('Not enough stock available.');
        }

        // Check if this book+format combination already in cart
        $existingItem = $cart->items()
            ->where('book_id', $bookId)
            ->where('format', $format)
            ->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $quantity;
            if ($format === 'physical' && $newQty > $book->stock_quantity) {
                throw new \Exception('Cannot exceed available stock.');
            }
            $existingItem->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'book_id'  => $bookId,
                'quantity' => $quantity,
                'format'   => $format,
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
                'items'        => [],
                'total'        => 0,
                'total_items'  => 0,
                'has_physical' => false,
            ];
        }

        $hasPhysical = false;
        $items = $cart->items->filter(function ($item) {
            return $item->book !== null;
        })->map(function ($item) use (&$hasPhysical) {
            $format = $item->format ?? 'physical';
            if ($format === 'physical') {
                $hasPhysical = true;
            }
            $price = $item->book->getPriceForFormat($format);
            
            return [
                'id'       => $item->id,
                'title'    => $item->book->title,
                'price'    => $price,
                'quantity' => $item->quantity,
                'format'   => $format,
                'image'    => $item->book->image && $item->book->image !== 'default.png' 
                    ? asset('storage/' . $item->book->image) 
                    : 'https://placehold.co/70x95/e2e8f0/64748b?text=' . urlencode($item->book->title),
            ];
        });

        $total = $items->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return [
            'items'        => $items->values(),
            'total'        => $total,
            'total_items'  => $items->sum('quantity'),
            'has_physical' => $hasPhysical,
        ];
    }
}
