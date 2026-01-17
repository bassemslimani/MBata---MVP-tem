<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display the client's reservations list.
     */
    public function index(Request $request): \Inertia\Response
    {
        $reservations = $request->user()
            ->reservations()
            ->with(['property.primaryImage', 'property.wilaya', 'payment', 'review'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $reservationsCollection = ReservationResource::collection($reservations);

        return Inertia::render('Client/Reservations/Index', [
            'reservations' => $reservationsCollection,
            'meta' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
        ]);
    }

    /**
     * Display the reservation details.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        $reservation = $request->user()
            ->reservations()
            ->with(['property.images', 'property.wilaya', 'property.commune', 'property.user', 'payment', 'review'])
            ->findOrFail($id);

        return Inertia::render('Client/Reservations/Show', [
            'reservation' => fn() => ReservationResource::make($reservation),
        ]);
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, string $id): RedirectResponse
    {
        $reservation = $request->user()->reservations()->findOrFail($id);

        if (!$reservation->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This reservation cannot be cancelled.');
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Display the reservation invoice.
     */
    public function invoice(Request $request, string $id): \Inertia\Response
    {
        $reservation = $request->user()
            ->reservations()
            ->with(['property.wilaya', 'property.commune', 'payment'])
            ->findOrFail($id);

        return Inertia::render('Client/Reservations/Invoice', [
            'reservation' => fn() => ReservationResource::make($reservation),
        ]);
    }
}
