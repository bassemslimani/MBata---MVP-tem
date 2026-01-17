<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationManagementController extends Controller
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
     * Display the reservations management page.
     */
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Admin/Reservations', [
            'filters' => $request->only(['status', 'from', 'to', 'check_in_from', 'check_in_to', 'property_id', 'client_id']),
            'apiEndpoint' => '/api/v1/admin/reservations',
        ]);
    }

    /**
     * Display reservation details.
     */
    public function show(Request $request, string $id): \Inertia\Response
    {
        return Inertia::render('Admin/Reservations/Show', [
            'reservationId' => $id,
            'apiEndpoint' => "/api/v1/admin/reservations/{$id}",
        ]);
    }
}
