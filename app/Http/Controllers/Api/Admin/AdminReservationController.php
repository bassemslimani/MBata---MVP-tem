<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    use ApiResponder;

    /**
     * List all reservations with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::with(['property.primaryImage', 'property.wilaya', 'client', 'payment']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date range
        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('created_at', [$request->input('from'), $request->input('to')]);
        }

        // Filter by check-in date range
        if ($request->has('check_in_from') && $request->has('check_in_to')) {
            $query->whereBetween('check_in_date', [$request->input('check_in_from'), $request->input('check_in_to')]);
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->input('property_id'));
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_user_id', $request->input('client_id'));
        }

        $reservations = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $reservationsCollection = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'check_in_date' => $reservation->check_in_date->toDateString(),
                'check_out_date' => $reservation->check_out_date->toDateString(),
                'guests_count' => $reservation->guests_count,
                'total_price_dzd' => $reservation->total_price_dzd,
                'currency_code' => $reservation->currency_code,
                'status' => $reservation->status,
                'special_requests' => $reservation->special_requests,
                'created_at' => $reservation->created_at->toDateTimeString(),
                'cancelled_at' => $reservation->cancelled_at?->toDateTimeString(),
                'property' => [
                    'id' => $reservation->property->id,
                    'title' => $reservation->property->title_fr,
                    'address' => $reservation->property->address,
                    'primary_image' => $reservation->property->primaryImage?->image_url,
                    'wilaya' => [
                        'id' => $reservation->property->wilaya->id,
                        'name' => $reservation->property->wilaya->name_fr,
                    ],
                ],
                'client' => [
                    'id' => $reservation->client->id,
                    'name' => $reservation->client->name,
                    'email' => $reservation->client->email,
                    'phone' => $reservation->client->phone,
                ],
                'payment' => $reservation->payment ? [
                    'id' => $reservation->payment->id,
                    'status' => $reservation->payment->status,
                    'payment_method' => $reservation->payment->payment_method,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reservationsCollection,
            'meta' => [
                'current_page' => $reservations->currentPage(),
                'last_page' => $reservations->lastPage(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
            ],
        ]);
    }

    /**
     * Get reservation details with client info.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $reservation = Reservation::with([
            'property.user',
            'property.wilaya',
            'property.commune',
            'property.images',
            'property.amenities',
            'client',
            'payment',
            'review',
        ])->findOrFail($id);

        return $this->successResponse(
            'reservation_data',
            [
                'id' => $reservation->id,
                'check_in_date' => $reservation->check_in_date->toDateString(),
                'check_out_date' => $reservation->check_out_date->toDateString(),
                'nights_count' => $reservation->check_in_date->diffInDays($reservation->check_out_date),
                'guests_count' => $reservation->guests_count,
                'total_price_dzd' => $reservation->total_price_dzd,
                'currency_code' => $reservation->currency_code,
                'status' => $reservation->status,
                'special_requests' => $reservation->special_requests,
                'created_at' => $reservation->created_at->toDateTimeString(),
                'updated_at' => $reservation->updated_at->toDateTimeString(),
                'cancelled_at' => $reservation->cancelled_at?->toDateTimeString(),
                'confirmed_at' => $reservation->confirmed_at?->toDateTimeString(),
                'can_be_cancelled' => $reservation->canBeCancelled(),
                'is_completed' => $reservation->isCompleted(),
                'property' => [
                    'id' => $reservation->property->id,
                    'title_fr' => $reservation->property->title_fr,
                    'title_ar' => $reservation->property->title_ar,
                    'title_en' => $reservation->property->title_en,
                    'address' => $reservation->property->address,
                    'price_per_night_dzd' => $reservation->property->price_per_night_dzd,
                    'max_guests' => $reservation->property->max_guests,
                    'owner' => [
                        'id' => $reservation->property->user->id,
                        'name' => $reservation->property->user->name,
                        'email' => $reservation->property->user->email,
                        'phone' => $reservation->property->user->phone,
                    ],
                    'location' => [
                        'wilaya' => [
                            'id' => $reservation->property->wilaya->id,
                            'name' => $reservation->property->wilaya->name_fr,
                        ],
                        'commune' => [
                            'id' => $reservation->property->commune->id,
                            'name' => $reservation->property->commune->name_fr,
                        ],
                    ],
                ],
                'client' => [
                    'id' => $reservation->client->id,
                    'name' => $reservation->client->name,
                    'email' => $reservation->client->email,
                    'phone' => $reservation->client->phone,
                    'avatar' => $reservation->client->avatar,
                ],
                'payment' => $reservation->payment ? [
                    'id' => $reservation->payment->id,
                    'status' => $reservation->payment->status,
                    'payment_method' => $reservation->payment->payment_method,
                    'amount_dzd' => $reservation->payment->amount_dzd,
                    'proof_image_url' => $reservation->payment->proof_image_url,
                    'admin_notes' => $reservation->payment->admin_notes,
                ] : null,
                'review' => $reservation->review ? [
                    'id' => $reservation->review->id,
                    'rating' => $reservation->review->rating,
                    'comment_fr' => $reservation->review->comment_fr,
                    'comment_ar' => $reservation->review->comment_ar,
                    'is_visible' => $reservation->review->is_visible,
                ] : null,
            ],
            200,
            $request->header('Accept-Language')
        );
    }
}
