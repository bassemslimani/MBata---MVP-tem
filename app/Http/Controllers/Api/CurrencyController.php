<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Get current exchange rates.
     */
    public function exchangeRates(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', 'fr');
        $nameColumn = match($locale) {
            'ar' => 'name_ar',
            'en' => 'name_en',
            default => 'name_fr',
        };

        $currencies = Currency::select('id', 'code', "{$nameColumn} as name", 'symbol', 'exchange_rate_to_dzd', 'is_default')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }

    /**
     * List all currencies.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->exchangeRates($request);
    }
}
