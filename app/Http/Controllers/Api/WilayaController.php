<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wilaya;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WilayaController extends Controller
{
    /**
     * List all wilayas.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', 'fr');
        $nameColumn = match($locale) {
            'ar' => 'name_ar',
            'en' => 'name_en',
            default => 'name_fr',
        };

        $wilayas = Wilaya::select('id', 'code', "{$nameColumn} as name", 'longitude', 'latitude')
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wilayas,
        ]);
    }

    /**
     * Get communes by wilaya.
     */
    public function communes(Request $request, string $id): JsonResponse
    {
        $locale = $request->header('Accept-Language', 'fr');
        $nameColumn = match($locale) {
            'ar' => 'name_ar',
            'en' => 'name_en',
            default => 'name_fr',
        };

        $wilaya = Wilaya::findOrFail($id);

        $communes = $wilaya->communes()
            ->select('id', 'code', "{$nameColumn} as name", 'postal_code')
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $communes,
        ]);
    }
}
