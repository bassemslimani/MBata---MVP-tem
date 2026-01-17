<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user() || !$request->user()->isSuperAdmin()) {
                abort(403, 'Access denied. Super admin privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Display the admins management page.
     */
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('SuperAdmin/Admins', [
            'apiEndpoint' => '/api/v1/super-admin/admins',
            'currentUserIsSuperAdmin' => $request->user()?->isSuperAdmin() ?? false,
        ]);
    }
}
