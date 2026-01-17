<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\PropertyDetailResource;
use App\Models\Property;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * Display the booking page for a property.
     */
    public function create(Request $request, string $id): \Inertia\Response
    {
        $property = Property::with([
            'propertyType',
            'propertyCategory',
            'wilaya',
            'commune',
            'amenities',
            'images' => fn($q) => $q->orderBy('order'),
            'primaryImage',
        ])
        ->where('is_active', true)
        ->findOrFail($id);

        // Get dates from query params
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $guests = $request->query('guests', 1);

        // Calculate price if dates are provided
        $totalPrice = null;
        $nights = null;
        if ($checkIn && $checkOut) {
            $checkInDate = Carbon::parse($checkIn);
            $checkOutDate = Carbon::parse($checkOut);
            $nights = $checkInDate->diffInDays($checkOutDate);
            $totalPrice = $property->price_per_night_dzd * $nights;
        }

        return Inertia::render('Property/Booking', [
            'property' => fn() => PropertyDetailResource::make($property),
            'booking' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'guests' => (int) $guests,
                'nights' => $nights,
                'total_price' => $totalPrice,
            ],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'phone' => $request->user()->phone,
                    'isClient' => $request->user()->isClient(),
                ] : null,
            ],
        ]);
    }

    /**
     * Store a new reservation.
     */
    public function store(StoreReservationRequest $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!$request->user() || !$request->user()->isClient()) {
            return redirect()->back()->with('error', 'Only clients can make reservations.');
        }

        try {
            $property = Property::active()->findOrFail($request->input('property_id'));
            $checkIn = Carbon::parse($request->input('check_in_date'));
            $checkOut = Carbon::parse($request->input('check_out_date'));
            $guestsCount = $request->integer('guests_count');

            // Validate max guests
            if ($guestsCount > $property->max_guests) {
                return redirect()->back()
                    ->with('error', "Property can only accommodate {$property->max_guests} guests.")
                    ->withInput();
            }

            // Check for overlapping reservations
            $overlappingCount = $property
                ->reservations()
                ->overlapping($checkIn->toDateString(), $checkOut->toDateString())
                ->count();

            if ($overlappingCount > 0) {
                return redirect()->back()
                    ->with('error', 'Property is not available for the selected dates.')
                    ->withInput();
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

            DB::commit();

            return redirect()->route('client.reservations.show', ['id' => $reservation->id])
                ->with('success', 'Reservation created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to create reservation. Please try again.')
                ->withInput();
        }
    }
}
