<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isAdmin()) {
                abort(403, 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Display the users management page.
     */
    public function index(Request $request): \Inertia\Response
    {
        // Get query parameters for filtering
        $params = [
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 20),
        ];

        if ($request->has('role')) {
            $params['role'] = $request->get('role');
        }

        if ($request->has('search')) {
            $params['search'] = $request->get('search');
        }

        if ($request->has('verified')) {
            $params['verified'] = $request->get('verified');
        }

        return Inertia::render('Admin/Users', [
            'filters' => $request->only(['role', 'search', 'verified']),
            'apiEndpoint' => '/api/v1/admin/users',
        ]);
    }

    /**
     * Display user details.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        return Inertia::render('Admin/Users/Show', [
            'userId' => $id,
            'apiEndpoint' => "/api/v1/admin/users/{$id}",
        ]);
    }
}
