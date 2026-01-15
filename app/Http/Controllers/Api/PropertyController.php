<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckAvailabilityRequest;
use App\Http\Requests\SearchPropertiesRequest;
use App\Http\Resources\PropertyDetailResource;
use App\Http\Resources\PropertySearchResource;
use App\Http\Resources\ReviewResource;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    /**
     * Search properties with filters.
     */
    public function index(SearchPropertiesRequest $request): JsonResponse
    {
        $query = Property::active()
            ->verified()
            ->with(['primaryImage', 'propertyType', 'wilaya', 'commune', 'visibleReviews']);

        // Filter by wilaya
        if ($request->has('wilaya_id')) {
            $query->where('wilaya_id', $request->input('wilaya_id'));
        }

        // Filter by commune
        if ($request->has('commune_id')) {
            $query->where('commune_id', $request->input('commune_id'));
        }

        // Filter by property type
        if ($request->has('property_type_id')) {
            $query->where('property_type_id', $request->input('property_type_id'));
        }

        // Filter by property category
        if ($request->has('property_category_id')) {
            $query->where('property_category_id', $request->input('property_category_id'));
        }

        // Filter by price range
        if ($request->has('price_min')) {
            $query->where('price_per_night_dzd', '>=', $request->input('price_min'));
        }
        if ($request->has('price_max')) {
            $query->where('price_per_night_dzd', '<=', $request->input('price_max'));
        }

        // Filter by guests capacity
        if ($request->has('guests')) {
            $query->where('max_guests', '>=', $request->input('guests'));
        }

        // Filter by amenities
        if ($request->has('amenities') && is_array($request->input('amenities'))) {
            $query->withAmenities($request->input('amenities'));
        }

        // Filter by availability for date range
        if ($request->has('check_in') && $request->has('check_out')) {
            $checkIn = Carbon::parse($request->input('check_in'))->toDateString();
            $checkOut = Carbon::parse($request->input('check_out'))->toDateString();
            $query->availableBetween($checkIn, $checkOut);
        }

        // Sorting
        $sort = $request->input('sort', 'created_desc');
        return match($sort) {
            'price_asc' => $query->orderBy('price_per_night_dzd'),
            'price_desc' => $query->orderByDesc('price_per_night_dzd'),
            'rating_desc' => $query->withCount('visibleReviews')->orderByDesc('visible_reviews_count'),
            'views_desc' => $query->orderByDesc('views_count'),
            default => $query->orderByDesc('created_at'),
        };

        $perPage = $request->input('per_page', 15);
        $properties = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PropertySearchResource::collection($properties),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ],
        ]);
    }

    /**
     * Get property details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $property = Property::active()
            ->with([
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
            ->findOrFail($id);

        // Increment views count
        $property->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => PropertyDetailResource::make($property),
        ]);
    }

    /**
     * Get property reviews.
     */
    public function reviews(Request $request, string $id): JsonResponse
    {
        $property = Property::active()->findOrFail($id);

        $reviews = $property
            ->visibleReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'average_rating' => round($property->averageRating(), 1),
            ],
        ]);
    }

    /**
     * Check availability and pricing for a date range.
     */
    public function availability(CheckAvailabilityRequest $request, string $id): JsonResponse
    {
        $property = Property::active()->findOrFail($id);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $nights = $startDate->diffInDays($endDate);

        // Check for overlapping reservations
        $overlappingReservations = $property
            ->reservations()
            ->overlapping(
                $startDate->toDateString(),
                $endDate->toDateString()
            )
            ->count();

        $isAvailable = $overlappingReservations === 0;

        // Check for price overrides in availabilities
        $priceOverrides = $property
            ->availabilities()
            ->whereBetween('start_date', [$startDate, $endDate])
            ->whereBetween('end_date', [$startDate, $endDate])
            ->where('is_available', true)
            ->get();

        $totalPriceDzd = $property->price_per_night_dzd * $nights;

        // Apply price overrides if any
        foreach ($priceOverrides as $override) {
            $overrideStart = Carbon::parse($override->start_date);
            $overrideEnd = Carbon::parse($override->end_date);

            $overlapStart = max($startDate, $overrideStart);
            $overlapEnd = min($endDate, $overrideEnd);

            if ($overlapEnd->gt($overlapStart)) {
                $nightsOverride = $overlapStart->diffInDays($overlapEnd);
                $originalPrice = $property->price_per_night_dzd * $nightsOverride;
                $overridePrice = $override->price_override_dzd * $nightsOverride;
                $totalPriceDzd = $totalPriceDzd - $originalPrice + $overridePrice;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'property_id' => $property->id,
                'is_available' => $isAvailable,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'nights_count' => $nights,
                'price_per_night_dzd' => $property->price_per_night_dzd,
                'total_price_dzd' => $totalPriceDzd,
                'blocked_dates' => $isAvailable ? [] : $this->getBlockedDates($property, $startDate, $endDate),
            ],
        ]);
    }

    /**
     * Get blocked dates for a property within a range.
     */
    protected function getBlockedDates(Property $property, Carbon $start, Carbon $end): array
    {
        $reservations = $property
            ->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->where('check_in_date', '<=', $end->toDateString())
                    ->where('check_out_date', '>=', $start->toDateString());
            })
            ->get(['check_in_date', 'check_out_date']);

        $blockedDates = [];
        foreach ($reservations as $reservation) {
            $current = Carbon::parse($reservation->check_in_date);
            $checkout = Carbon::parse($reservation->check_out_date);

            while ($current->lt($checkout)) {
                $blockedDates[] = $current->toDateString();
                $current->addDay();
            }
        }

        return array_values(array_unique($blockedDates));
    }
}
