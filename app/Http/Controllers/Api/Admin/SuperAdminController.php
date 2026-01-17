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

class SuperAdminController extends Controller
{
    use ApiResponder;

    /**
     * Ensure the user is a super admin.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Super admin only.',
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * List all admin users.
     */
    public function indexAdmins(Request $request): JsonResponse
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $adminsCollection = $admins->map(function ($admin) {
            return [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'created_at' => $admin->created_at?->toDateTimeString(),
                'last_login' => null, // Could be tracked separately
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $adminsCollection,
            'meta' => [
                'current_page' => $admins->currentPage(),
                'last_page' => $admins->lastPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
            ],
        ]);
    }

    /**
     * Create a new admin user.
     */
    public function storeAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'locale' => ['nullable', 'in:fr,ar,en'],
        ]);

        DB::beginTransaction();

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'admin',
            'locale' => $validated['locale'] ?? 'fr',
        ]);

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_CREATE,
            AdminAction::ENTITY_USER,
            $admin->id,
            null,
            [
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'admin',
            ],
            'New admin account created'
        );

        DB::commit();

        return $this->successResponse(
            'admin_created',
            [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => $admin->role,
                'created_at' => $admin->created_at->toDateTimeString(),
            ],
            201,
            $request->header('Accept-Language')
        );
    }

    /**
     * Remove admin role from a user (change to client).
     */
    public function destroyAdmin(Request $request, string $id): JsonResponse
    {
        $admin = User::where('role', 'admin')
            ->findOrFail($id);

        // Cannot remove yourself from admin
        if ($admin->id === $request->user()->id) {
            return $this->errorResponse(
                'cannot_remove_self',
                400,
                $request->header('Accept-Language')
            );
        }

        $oldRole = $admin->role;

        DB::beginTransaction();

        $admin->update(['role' => 'client']);

        // Log the admin action
        AdminAction::log(
            $request->user()->id,
            AdminAction::ACTION_UPDATE,
            AdminAction::ENTITY_USER,
            $admin->id,
            ['role' => $oldRole],
            ['role' => 'client'],
            'Removed admin role'
        );

        DB::commit();

        return $this->successResponse(
            'admin_removed',
            [
                'id' => $admin->id,
                'name' => $admin->name,
                'role' => 'client',
            ],
            200,
            $request->header('Accept-Language')
        );
    }
}
