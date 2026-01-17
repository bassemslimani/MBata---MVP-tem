<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CurrencySettingsController extends Controller
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
     * Display the currency settings page.
     */
    public function edit(Request $request): \Inertia\Response
    {
        // Fetch exchange rates from API
        $response = Http::withToken($request->session()->get('token'))
            ->get(config('app.url') . '/api/v1/currencies/exchange-rates');

        $currencies = $response->successful() ? $response->json()['data'] : null;

        return Inertia::render('Admin/Settings/Currency', [
            'currencies' => $currencies,
            'apiEndpoint' => '/api/v1/currencies',
        ]);
    }
}
