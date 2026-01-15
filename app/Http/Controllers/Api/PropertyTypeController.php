<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyTypeResource;
use App\Models\PropertyType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    /**
     * List all property types.
     */
    public function index(Request $request): JsonResponse
    {
        $types = PropertyType::active()
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PropertyTypeResource::collection($types),
        ]);
    }
}
