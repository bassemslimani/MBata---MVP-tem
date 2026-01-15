<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OwnerPropertyController extends Controller
{
    /**
     * List owner's properties.
     */
    public function index(Request $request): JsonResponse
    {
        $properties = $request->user()
            ->properties()
            ->with(['primaryImage', 'propertyType', 'propertyCategory', 'wilaya', 'commune'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => PropertyResource::collection($properties),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    /**
     * Store a new property.
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $property = $request->user()->properties()->create($request->validated());

            if ($request->has('amenity_ids')) {
                $property->amenities()->attach($request->input('amenity_ids'));
            }

            $property->load(['propertyType', 'propertyCategory', 'wilaya', 'commune', 'amenities']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Property created successfully.',
                'data' => PropertyResource::make($property),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create property.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $property = $request->user()
            ->properties()
            ->with(['propertyType', 'propertyCategory', 'wilaya', 'commune', 'amenities', 'images' => fn($q) => $q->orderBy('order')])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => PropertyResource::make($property),
        ]);
    }

    /**
     * Update property.
     */
    public function update(UpdatePropertyRequest $request, string $id): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($id);

            DB::beginTransaction();

            $property->update($request->validated());

            if ($request->has('amenity_ids')) {
                $property->amenities()->sync($request->input('amenity_ids'));
            }

            $property->load(['propertyType', 'propertyCategory', 'wilaya', 'commune', 'amenities']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully.',
                'data' => PropertyResource::make($property),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update property.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete property.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($id);

            // Check if property has active reservations
            if ($property->reservations()->whereIn('status', ['pending', 'confirmed'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete property with active reservations.',
                ], 400);
            }

            $property->delete();

            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete property.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload property image.
     */
    public function uploadImage(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:5120'],
            'is_primary' => ['boolean'],
            'order' => ['integer', 'min:0'],
            'alt_text_fr' => ['nullable', 'string', 'max:255'],
            'alt_text_ar' => ['nullable', 'string', 'max:255'],
            'alt_text_en' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $property = $request->user()->properties()->findOrFail($id);

            DB::beginTransaction();

            $path = $request->file('image')->store('properties/' . $property->id, 'public');

            $isPrimary = $request->boolean('is_primary', false);

            if ($isPrimary) {
                $property->images()->update(['is_primary' => false]);
            }

            $image = $property->images()->create([
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'order' => $request->integer('order', 0),
                'alt_text_fr' => $request->input('alt_text_fr'),
                'alt_text_ar' => $request->input('alt_text_ar'),
                'alt_text_en' => $request->input('alt_text_en'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully.',
                'data' => $image,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete property image.
     */
    public function deleteImage(Request $request, string $propertyId, string $imageId): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($propertyId);
            $image = $property->images()->findOrFail($imageId);

            Storage::disk('public')->delete($image->image_path);
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set primary image.
     */
    public function setPrimaryImage(Request $request, string $propertyId, string $imageId): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($propertyId);
            $image = $property->images()->findOrFail($imageId);

            DB::beginTransaction();

            $property->images()->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Primary image set successfully.',
                'data' => $image,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
