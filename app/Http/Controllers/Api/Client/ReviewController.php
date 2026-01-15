<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Submit a review.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        try {
            $reservation = Reservation::where('id', $request->input('reservation_id'))
                ->where('client_user_id', $request->user()->id)
                ->firstOrFail();

            // Check if reservation belongs to the property
            if ($reservation->property_id != $request->input('property_id')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation does not match the property.',
                ], 400);
            }

            // Check if reservation is completed
            if (!$reservation->isCompleted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only review completed stays.',
                ], 400);
            }

            // Check if review already exists
            if ($reservation->review) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this reservation.',
                ], 400);
            }

            DB::beginTransaction();

            $review = $reservation->review()->create([
                'property_id' => $request->input('property_id'),
                'user_id' => $request->user()->id,
                'rating' => $request->input('rating'),
                'comment_fr' => $request->input('comment_fr'),
                'comment_ar' => $request->input('comment_ar'),
                'is_visible' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully.',
                'data' => ReviewResource::make($review->load('user')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's reviews.
     */
    public function index(Request $request): JsonResponse
    {
        $reviews = $request->user()
            ->reviews()
            ->with(['property.primaryImage', 'property.wilaya'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Get a specific review.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $review = $request->user()
            ->reviews()
            ->with(['property', 'reservation'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => ReviewResource::make($review),
        ]);
    }
}
