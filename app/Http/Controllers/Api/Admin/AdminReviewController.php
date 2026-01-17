<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Models\AdminAction;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    use ApiResponder;

    /**
     * List all reviews with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Review::with(['property.wilaya', 'property.primaryImage', 'user']);

        // Filter by visibility
        if ($request->has('visible')) {
            $query->where('is_visible', $request->boolean('visible'));
        }

        // Filter by rating
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->input('min_rating'));
        }

        if ($request->has('max_rating')) {
            $query->where('rating', '<=', $request->input('max_rating'));
        }

        // Filter by property
        if ($request->has('property_id')) {
            $query->where('property_id', $request->input('property_id'));
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $reviews = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        $reviewsCollection = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment_fr' => $review->comment_fr,
                'comment_ar' => $review->comment_ar,
                'is_visible' => $review->is_visible,
                'created_at' => $review->created_at->toDateTimeString(),
                'property' => [
                    'id' => $review->property->id,
                    'title' => $review->property->title_fr,
                    'primary_image' => $review->property->primaryImage?->image_url,
                    'wilaya' => [
                        'id' => $review->property->wilaya->id,
                        'name' => $review->property->wilaya->name_fr,
                    ],
                ],
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'email' => $review->user->email,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reviewsCollection,
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Get review details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $review = Review::with([
            'property.user',
            'property.wilaya',
            'property.commune',
            'user',
            'reservation',
        ])->findOrFail($id);

        return $this->successResponse(
            'review_data',
            [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment_fr' => $review->comment_fr,
                'comment_ar' => $review->comment_ar,
                'is_visible' => $review->is_visible,
                'created_at' => $review->created_at->toDateTimeString(),
                'updated_at' => $review->updated_at->toDateTimeString(),
                'property' => [
                    'id' => $review->property->id,
                    'title_fr' => $review->property->title_fr,
                    'title_ar' => $review->property->title_ar,
                    'title_en' => $review->property->title_en,
                    'address' => $review->property->address,
                    'owner' => [
                        'id' => $review->property->user->id,
                        'name' => $review->property->user->name,
                        'email' => $review->property->user->email,
                    ],
                    'wilaya' => [
                        'id' => $review->property->wilaya->id,
                        'name' => $review->property->wilaya->name_fr,
                    ],
                ],
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'email' => $review->user->email,
                    'phone' => $review->user->phone,
                ],
                'reservation' => $review->reservation ? [
                    'id' => $review->reservation->id,
                    'check_in_date' => $review->reservation->check_in_date->toDateString(),
                    'check_out_date' => $review->reservation->check_out_date->toDateString(),
                    'status' => $review->reservation->status,
                ] : null,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Toggle review visibility.
     */
    public function toggleVisibility(Request $request, string $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        $oldValue = ['is_visible' => $review->is_visible];
        $newValue = ['is_visible' => !$review->is_visible];

        $review->update(['is_visible' => !$review->is_visible]);

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_TOGGLE_VISIBILITY,
            AdminAction::ENTITY_REVIEW,
            $review->id,
            $oldValue,
            $newValue
        );

        return $this->successResponse(
            'review_visibility_toggled',
            [
                'id' => $review->id,
                'is_visible' => $review->is_visible,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Delete a review.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        // Store review info before deletion
        $reviewInfo = [
            'id' => $review->id,
            'rating' => $review->rating,
            'property_id' => $review->property_id,
            'user_id' => $review->user_id,
        ];

        $review->delete();

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_DELETE,
            AdminAction::ENTITY_REVIEW,
            $review->id,
            $reviewInfo,
            null
        );

        return $this->successResponse(
            'review_deleted',
            null,
            200,
            $request->header('Accept-Language')
        );
    }
}
