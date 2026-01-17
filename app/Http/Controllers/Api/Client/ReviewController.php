<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
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
    use ApiResponder;

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
                return $this->errorResponse(
                    'reservation_not_found',
                    400,
                    $request->header('Accept-Language')
                );
            }

            // Check if reservation is completed
            if (!$reservation->isCompleted()) {
                return $this->errorResponse(
                    'review_completed_stay_only',
                    400,
                    $request->header('Accept-Language')
                );
            }

            // Check if review already exists
            if ($reservation->review) {
                return $this->errorResponse(
                    'review_already_exists',
                    400,
                    $request->header('Accept-Language')
                );
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

            return $this->successResponse(
                'review_submitted',
                ReviewResource::make($review->load('user')),
                201,
                $request->header('Accept-Language')
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                'save_failed',
                500,
                $request->header('Accept-Language')
            );
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
