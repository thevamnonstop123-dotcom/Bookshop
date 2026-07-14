<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'book_id'  => 'required|exists:books,id',
            'quantity' => 'required|integer|min:1',
            'format'   => 'nullable|in:physical,ebook',
        ]);

        try {
            $this->cartService->addItem(
                auth('customer')->id(),
                $request->book_id,
                $request->quantity,
                $request->format ?? 'physical'
            );

            $cart = $this->cartService->getCart(auth('customer')->id());

            return response()->json([
                'message' => 'Item added to cart.',
                'cart'    => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $this->cartService->updateItem($cartItemId, $request->quantity);

        $cart = $this->cartService->getCart(auth('customer')->id());

        return response()->json([
            'message' => 'Cart updated.',
            'cart'    => $cart,
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove($cartItemId)
    {
        $this->cartService->removeItem($cartItemId);

        $cart = $this->cartService->getCart(auth('customer')->id());

        return response()->json([
            'message' => 'Item removed.',
            'cart'    => $cart,
        ]);
    }

    /**
     * Get cart data for initial page load.
     */
    public function getData()
    {
        $cart = $this->cartService->getCart(auth('customer')->id());

        return response()->json(['cart' => $cart]);
    }
}
