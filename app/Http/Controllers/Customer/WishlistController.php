<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\WishlistService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Toggle wishlist via AJAX.
     */
    public function toggle(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);

        $added = $this->wishlistService->toggle(
            auth('customer')->id(),
            $request->book_id
        );

        $count = $this->wishlistService->getWishlistedIds(auth('customer')->id());

        return response()->json([
            'success' => true,
            'added' => $added,
            'count' => count($count),
            'message' => $added ? 'Added to wishlist!' : 'Removed from wishlist.',
        ]);
    }

    /**
     * Show wishlist page.
     */
    public function index()
    {
        $wishlists = $this->wishlistService->getWishlist(auth('customer')->id());
        $wishlistedIds = $this->wishlistService->getWishlistedIds(auth('customer')->id());

        return view('customer.wishlist.index', compact('wishlists', 'wishlistedIds'));
    }

    /**
     * Remove item from wishlist.
     */
    public function remove($bookId)
    {
        $removed = $this->wishlistService->remove(
            auth('customer')->id(),
            $bookId
        );

        return response()->json([
            'success' => $removed,
            'message' => $removed ? 'Removed from wishlist.' : 'Item not found.',
        ]);
    }
}
