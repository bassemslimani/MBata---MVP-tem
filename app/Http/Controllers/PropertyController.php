<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyDetailResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyController extends Controller
{
    /**
     * Display the property details page.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        $property = Property::with([
            'propertyType',
            'propertyCategory',
            'wilaya',
            'commune',
            'amenities',
            'images' => fn($q) => $q->orderBy('order'),
            'primaryImage',
            'user',
            'visibleReviews' => fn($q) => $q->latest()->limit(10),
        ])
        ->where('is_active', true)
        ->findOrFail($id);

        // Increment views count
        $property->increment('views_count');

        // Check if property is favorited by authenticated user
        $isFavorited = false;
        if ($request->user()) {
            $isFavorited = $request->user()->favorites()->where('property_id', $id)->exists();
        }

        return Inertia::render('Property/Show', [
            'property' => fn() => PropertyDetailResource::make($property),
            'isFavorited' => $isFavorited,
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'isClient' => $request->user()->isClient(),
                ] : null,
            ],
        ]);
    }
}
