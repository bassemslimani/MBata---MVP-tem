<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DashboardController extends Controller
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
     * Display the admin dashboard.
     */
    public function index(Request $request): \Inertia\Response
    {
        // Fetch dashboard stats from API
        $response = Http::withToken($request->session()->get('token'))
            ->get(config('app.url') . '/api/v1/admin/dashboard');

        $stats = $response->successful() ? $response->json()['data'] : null;

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
        ]);
    }
}
