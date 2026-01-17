<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PropertyManagementController extends Controller
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
     * Display the properties management page.
     */
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Admin/Properties', [
            'filters' => $request->only(['verified', 'active', 'wilaya_id', 'owner_id', 'search']),
            'apiEndpoint' => '/api/v1/admin/properties',
        ]);
    }

    /**
     * Display property details.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        return Inertia::render('Admin/Properties/Show', [
            'propertyId' => $id,
            'apiEndpoint' => "/api/v1/admin/properties/{$id}",
        ]);
    }
}
