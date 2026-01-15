<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyCategoryResource;
use App\Models\PropertyCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyCategoryController extends Controller
{
    /**
     * List all property categories.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = PropertyCategory::active()
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PropertyCategoryResource::collection($categories),
        ]);
    }
}
