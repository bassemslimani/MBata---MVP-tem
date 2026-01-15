<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AmenityResource;
use App\Models\Amenity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    /**
     * List all amenities.
     */
    public function index(Request $request): JsonResponse
    {
        $amenities = Amenity::active()
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => AmenityResource::collection($amenities),
        ]);
    }
}
