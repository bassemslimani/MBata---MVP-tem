<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Models\AdminAction;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPropertyController extends Controller
{
    use ApiResponder;

    /**
     * List all properties with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Property::with(['user', 'wilaya', 'primaryImage']);

        // Filter by verification status
        if ($request->has('verified')) {
            $query->where('is_verified', $request->boolean('verified'));
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Filter by wilaya
        if ($request->has('wilaya_id')) {
            $query->where('wilaya_id', $request->input('wilaya_id'));
        }

        // Filter by owner
        if ($request->has('owner_id')) {
            $query->where('user_id', $request->input('owner_id'));
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title_fr', 'like', "%{$search}%")
                    ->orWhere('title_ar', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $properties = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $propertiesCollection = $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'title' => $property->title_fr,
                'title_fr' => $property->title_fr,
                'title_ar' => $property->title_ar,
                'title_en' => $property->title_en,
                'address' => $property->address,
                'price_per_night_dzd' => $property->price_per_night_dzd,
                'max_guests' => $property->max_guests,
                'bedrooms' => $property->bedrooms,
                'is_active' => $property->is_active,
                'is_verified' => $property->is_verified,
                'views_count' => $property->views_count,
                'created_at' => $property->created_at?->toDateTimeString(),
                'owner' => [
                    'id' => $property->user->id,
                    'name' => $property->user->name,
                    'email' => $property->user->email,
                ],
                'wilaya' => [
                    'id' => $property->wilaya->id,
                    'name' => $property->wilaya->name_fr,
                ],
                'primary_image' => $property->primaryImage?->image_url,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $propertiesCollection,
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    /**
     * Get property details with owner info.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $property = Property::with([
            'user',
            'wilaya',
            'commune',
            'propertyType',
            'propertyCategory',
            'amenities',
            'images' => fn($q) => $q->orderBy('order'),
            'primaryImage',
            'availabilities',
        ])->findOrFail($id);

        // Get reservation stats for this property
        $reservationStats = [
            'total' => $property->reservations()->count(),
            'pending' => $property->reservations()->where('status', 'pending')->count(),
            'confirmed' => $property->reservations()->where('status', 'confirmed')->count(),
            'completed' => $property->reservations()->where('status', 'completed')->count(),
            'cancelled' => $property->reservations()->where('status', 'cancelled')->count(),
        ];

        return $this->successResponse(
            'property_data',
            [
                'id' => $property->id,
                'title_fr' => $property->title_fr,
                'title_ar' => $property->title_ar,
                'title_en' => $property->title_en,
                'description_fr' => $property->description_fr,
                'description_ar' => $property->description_ar,
                'description_en' => $property->description_en,
                'address' => $property->address,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'price_per_night_dzd' => $property->price_per_night_dzd,
                'surface_area' => $property->surface_area,
                'rooms' => $property->rooms,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'max_guests' => $property->max_guests,
                'is_active' => $property->is_active,
                'is_verified' => $property->is_verified,
                'views_count' => $property->views_count,
                'created_at' => $property->created_at?->toDateTimeString(),
                'updated_at' => $property->updated_at?->toDateTimeString(),
                'owner' => [
                    'id' => $property->user->id,
                    'name' => $property->user->name,
                    'email' => $property->user->email,
                    'phone' => $property->user->phone,
                ],
                'location' => [
                    'wilaya' => [
                        'id' => $property->wilaya->id,
                        'name' => $property->wilaya->name_fr,
                        'code' => $property->wilaya->code,
                    ],
                    'commune' => [
                        'id' => $property->commune->id,
                        'name' => $property->commune->name_fr,
                    ],
                ],
                'type' => [
                    'id' => $property->propertyType->id,
                    'name' => $property->propertyType->name,
                ],
                'category' => [
                    'id' => $property->propertyCategory->id,
                    'name' => $property->propertyCategory->name,
                ],
                'amenities' => $property->amenities->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->name,
                ]),
                'images' => $property->images->map(fn($i) => [
                    'id' => $i->id,
                    'image_url' => $i->image_url,
                    'is_primary' => $i->is_primary,
                    'order' => $i->order,
                ]),
                'reservation_stats' => $reservationStats,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Verify a property.
     */
    public function verify(Request $request, string $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        if ($property->is_verified) {
            return $this->errorResponse(
                'property_already_verified',
                400,
                $request->header('Accept-Language')
            );
        }

        $oldValue = ['is_verified' => false];
        $newValue = ['is_verified' => true];

        $property->update(['is_verified' => true]);

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_VERIFY,
            AdminAction::ENTITY_PROPERTY,
            $property->id,
            $oldValue,
            $newValue
        );

        return $this->successResponse(
            'property_verified',
            [
                'id' => $property->id,
                'is_verified' => true,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Delete a property.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $property = Property::findOrFail($id);

        DB::beginTransaction();

        // Store property info before deletion
        $propertyInfo = [
            'id' => $property->id,
            'title' => $property->title_fr,
            'owner_id' => $property->user_id,
        ];

        $property->delete();

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_DELETE,
            AdminAction::ENTITY_PROPERTY,
            $property->id,
            $propertyInfo,
            null
        );

        DB::commit();

        return $this->successResponse(
            'property_deleted',
            null,
            200,
            $request->header('Accept-Language')
        );
    }
}
