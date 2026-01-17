<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewManagementController extends Controller
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
     * Display the reviews management page.
     */
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Admin/Reviews', [
            'filters' => $request->only(['visible', 'min_rating', 'max_rating', 'property_id', 'user_id']),
            'apiEndpoint' => '/api/v1/admin/reviews',
        ]);
    }

    /**
     * Display review details.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        return Inertia::render('Admin/Reviews/Show', [
            'reviewId' => $id,
            'apiEndpoint' => "/api/v1/admin/reviews/{$id}",
        ]);
    }
}
