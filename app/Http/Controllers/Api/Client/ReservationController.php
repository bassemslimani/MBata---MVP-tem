<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Http\Requests\StoreReservationRequest;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * List client's reservations.
     */
    public function index(Request $request): JsonResponse
    {
        $reservations = $request->user()
            ->reservations()
            ->with(['property.primaryImage', 'property.wilaya', 'payment', 'review'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => ReservationResource::collection($reservations),
            'meta' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
        ]);
    }

    /**
     * Create a new reservation.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $property = Property::active()->findOrFail($request->input('property_id'));
            $checkIn = Carbon::parse($request->input('check_in_date'));
            $checkOut = Carbon::parse($request->input('check_out_date'));
            $guestsCount = $request->integer('guests_count');

            // Validate max guests
            if ($guestsCount > $property->max_guests) {
                return response()->json([
                    'success' => false,
                    'message' => "Property can only accommodate {$property->max_guests} guests.",
                ], 400);
            }

            // Check for overlapping reservations
            $overlappingCount = $property
                ->reservations()
                ->overlapping($checkIn->toDateString(), $checkOut->toDateString())
                ->count();

            if ($overlappingCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property is not available for the selected dates.',
                ], 400);
            }

            DB::beginTransaction();

            // Calculate total price
            $nights = $checkIn->diffInDays($checkOut);
            $totalPriceDzd = $property->price_per_night_dzd * $nights;

            // Check for price overrides
            $priceOverrides = $property
                ->availabilities()
                ->whereBetween('start_date', [$checkIn, $checkOut])
                ->where('is_available', true)
                ->whereNotNull('price_override_dzd')
                ->get();

            foreach ($priceOverrides as $override) {
                $overrideStart = Carbon::parse($override->start_date);
                $overrideEnd = Carbon::parse($override->end_date);
                $overlapStart = max($checkIn, $overrideStart);
                $overlapEnd = min($checkOut, $overrideEnd);

                if ($overlapEnd->gt($overlapStart)) {
                    $nightsOverride = $overlapStart->diffInDays($overlapEnd);
                    $originalPrice = $property->price_per_night_dzd * $nightsOverride;
                    $overridePrice = $override->price_override_dzd * $nightsOverride;
                    $totalPriceDzd = $totalPriceDzd - $originalPrice + $overridePrice;
                }
            }

            $reservation = $request->user()->reservations()->create([
                'property_id' => $property->id,
                'check_in_date' => $checkIn->toDateString(),
                'check_out_date' => $checkOut->toDateString(),
                'guests_count' => $guestsCount,
                'total_price_dzd' => $totalPriceDzd,
                'currency_code' => 'DZD',
                'status' => 'pending',
                'special_requests' => $request->input('special_requests'),
            ]);

            $reservation->load('property.primaryImage', 'property.wilaya');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully.',
                'data' => ReservationResource::make($reservation),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get reservation details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $reservation = $request->user()
            ->reservations()
            ->with(['property.images', 'property.wilaya', 'property.commune', 'property.user', 'payment', 'review'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ReservationResource::make($reservation),
        ]);
    }

    /**
     * Cancel reservation.
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        try {
            $reservation = $request->user()->reservations()->findOrFail($id);

            if (!$reservation->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reservation cannot be cancelled.',
                ], 400);
            }

            $reservation->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully.',
                'data' => ReservationResource::make($reservation),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel reservation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
