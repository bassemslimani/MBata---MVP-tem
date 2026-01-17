<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    /**
     * Display the user's favorites list.
     */
    public function index(Request $request): \Inertia\Response
    {
        $favorites = $request->user()
            ->favorites()
            ->with(['property.primaryImage', 'property.wilaya', 'property.visibleReviews'])
            ->latest()
            ->paginate(20);

        $favoritesCollection = FavoriteResource::collection($favorites);

        return Inertia::render('Client/Favorites', [
            'favorites' => $favoritesCollection,
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
            ],
        ]);
    }
}
