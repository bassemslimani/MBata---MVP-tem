<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use App\Models\PropertyCategory;
use App\Models\Wilaya;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Display the search page with filter options.
     */
    public function index(Request $request): \Inertia\Response
    {
        // Get filter data for the search form
        $filters = [
            'wilayas' => Wilaya::orderBy('name')->get(['id', 'name', 'code']),
            'property_types' => PropertyType::orderBy('name')->get(['id', 'name']),
            'property_categories' => PropertyCategory::orderBy('name')->get(['id', 'name']),
            'amenities' => Amenity::orderBy('name')->get(['id', 'name', 'icon']),
        ];

        // Get active filters from query params
        $activeFilters = [
            'wilaya_id' => $request->query('wilaya_id'),
            'commune_id' => $request->query('commune_id'),
            'property_type_id' => $request->query('property_type_id'),
            'property_category_id' => $request->query('property_category_id'),
            'check_in' => $request->query('check_in'),
            'check_out' => $request->query('check_out'),
            'guests' => $request->query('guests'),
            'price_min' => $request->query('price_min'),
            'price_max' => $request->query('price_max'),
            'amenities' => $request->query('amenities') ? explode(',', $request->query('amenities')) : [],
            'sort' => $request->query('sort', 'created_desc'),
        ];

        return Inertia::render('Search', [
            'filters' => $filters,
            'activeFilters' => array_filter($activeFilters, fn($value) => $value !== null && $value !== ''),
        ]);
    }
}
