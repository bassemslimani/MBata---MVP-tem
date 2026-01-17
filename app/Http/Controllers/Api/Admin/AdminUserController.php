<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponder;
use App\Models\AdminAction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    use ApiResponder;

    /**
     * List all users with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->has('verified')) {
            if ($request->boolean('verified')) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->withCount(['properties', 'reservations'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $usersCollection = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'locale' => $user->locale,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
                'properties_count' => $user->properties_count,
                'reservations_count' => $user->reservations_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $usersCollection,
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get user details with properties and reservations count.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = User::with(['properties' => fn($q) => $q->withCount('reservations', 'reviews')])
            ->withCount(['properties', 'reservations', 'reviews'])
            ->findOrFail($id);

        // Get user's recent reservations
        $recentReservations = $user->reservations()
            ->with('property')
            ->latest()
            ->limit(5)
            ->get(['id', 'property_id', 'check_in_date', 'check_out_date', 'status', 'total_price_dzd', 'created_at']);

        // Get user's properties with their stats
        $properties = $user->properties()
            ->withCount('reservations', 'reviews')
            ->get(['id', 'title_fr', 'is_active', 'is_verified', 'created_at']);

        return $this->successResponse(
            'user_data',
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'locale' => $user->locale,
                'avatar' => $user->avatar,
                'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
                'updated_at' => $user->updated_at?->toDateTimeString(),
                'stats' => [
                    'properties_count' => $user->properties_count,
                    'reservations_count' => $user->reservations_count,
                    'reviews_count' => $user->reviews_count,
                ],
                'recent_reservations' => $recentReservations,
                'properties' => $properties,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Toggle user status (suspend/activate).
     */
    public function toggleStatus(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent modifying super admin
        if ($user->isSuperAdmin()) {
            return $this->errorResponse(
                'forbidden',
                403,
                $request->header('Accept-Language')
            );
        }

        $oldStatus = $user->is_active ?? true;
        $newStatus = !$oldStatus;

        $user->update(['is_active' => $newStatus]);

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            $newStatus ? AdminAction::ACTION_ACTIVATE : AdminAction::ACTION_SUSPEND,
            AdminAction::ENTITY_USER,
            $user->id,
            ['is_active' => $oldStatus],
            ['is_active' => $newStatus]
        );

        return $this->successResponse(
            $newStatus ? 'user_activated' : 'user_suspended',
            [
                'id' => $user->id,
                'is_active' => $newStatus,
            ],
            200,
            $request->header('Accept-Language')
        );
    }

    /**
     * Delete a user (soft delete).
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting super admin or self
        if ($user->isSuperAdmin()) {
            return $this->errorResponse(
                'forbidden',
                403,
                $request->header('Accept-Language')
            );
        }

        if ($user->id === $request->user()->id) {
            return $this->errorResponse(
                'cannot_delete_self',
                400,
                $request->header('Accept-Language')
            );
        }

        DB::beginTransaction();

        // Store user info before deletion
        $userInfo = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $user->delete();

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_DELETE,
            AdminAction::ENTITY_USER,
            $user->id,
            $userInfo,
            null
        );

        DB::commit();

        return $this->successResponse(
            'user_deleted',
            null,
            200,
            $request->header('Accept-Language')
        );
    }
}
