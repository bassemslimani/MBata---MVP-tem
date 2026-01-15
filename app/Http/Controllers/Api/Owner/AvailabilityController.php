<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvailabilityRequest;
use App\Http\Requests\UpdateAvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Availability;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvailabilityController extends Controller
{
    /**
     * Get property availabilities.
     */
    public function index(Request $request, string $propertyId): JsonResponse
    {
        $property = $request->user()->properties()->findOrFail($propertyId);

        $availabilities = $property->availabilities()
            ->orderBy('start_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AvailabilityResource::collection($availabilities),
        ]);
    }

    /**
     * Store availability.
     */
    public function store(StoreAvailabilityRequest $request, string $propertyId): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($propertyId);

            $availability = $property->availabilities()->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Availability created successfully.',
                'data' => AvailabilityResource::make($availability),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create availability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update availability.
     */
    public function update(UpdateAvailabilityRequest $request, string $propertyId, string $id): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($propertyId);
            $availability = $property->availabilities()->findOrFail($id);

            $availability->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Availability updated successfully.',
                'data' => AvailabilityResource::make($availability),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete availability.
     */
    public function destroy(Request $request, string $propertyId, string $id): JsonResponse
    {
        try {
            $property = $request->user()->properties()->findOrFail($propertyId);
            $availability = $property->availabilities()->findOrFail($id);

            $availability->delete();

            return response()->json([
                'success' => true,
                'message' => 'Availability deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete availability.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
