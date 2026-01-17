<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponder;

    /**
     * List user's favorites.
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['property.primaryImage', 'property.wilaya', 'property.visibleReviews'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => FavoriteResource::collection($favorites),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }

    /**
     * Add property to favorites.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
        ]);

        $favorite = $request->user()->favorites()->firstOrCreate([
            'property_id' => $request->input('property_id'),
        ]);

        $messageKey = $favorite->wasRecentlyCreated
            ? 'added_to_favorites'
            : 'already_in_favorites';

        return $this->successResponse(
            $messageKey,
            FavoriteResource::make($favorite->load('property.primaryImage')),
            $favorite->wasRecentlyCreated ? 201 : 200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Remove from favorites.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $favorite = $request->user()
            ->favorites()
            ->findOrFail($id);

        $favorite->delete();

        return $this->successResponse(
            'removed_from_favorites',
            null,
            200,
            $request->header('Accept-Language')
        );
    }
}
