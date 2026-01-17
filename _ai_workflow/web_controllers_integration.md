# Web Controllers Integration Guide

**Purpose:** Document all props that frontend Vue pages expect from Laravel web controllers.

**Last Updated:** 2025-01-15 (Sprint 5)

---

## Controller → Page Prop Mapping

### 1. SearchController → Search.vue
**Route:** `GET /search`
**Layout:** `AppLayout`

**Required Props:**
```php
return Inertia::render('Search', [
    // Search filters
    'filters' => [
        'destination' => $request->input('destination'),
        'check_in' => $request->input('check_in'),
        'check_out' => $request->input('check_out'),
        'guests' => $request->input('guests', 1),
        'min_price' => $request->input('min_price'),
        'max_price' => $request->input('max_price'),
        'bedrooms' => $request->input('bedrooms'),
        'property_type_id' => $request->input('property_type_id'),
        'amenities' => $request->input('amenities', []), // array
    ],

    // Search results (collection)
    'properties' => $properties->map(fn($p) => [
        'id' => $p->id,
        'title' => $p->title,
        'description' => $p->description,
        'wilaya' => $p->wilaya,
        'commune' => $p->commune,
        'property_type' => $p->property_type->name ?? null,
        'max_guests' => $p->max_guests,
        'bedrooms' => $p->bedrooms,
        'bathrooms' => $p->bathrooms,
        'price_per_night_dzd' => $p->price_per_night_dzd,
        'thumbnail_url' => $p->thumbnail_url,
        'images' => $p->images->map(fn($i) => [
            'id' => $i->id,
            'url' => $i->url,
        ]),
        'average_rating' => $p->average_rating,
        'reviews_count' => $p->reviews_count,
        'amenities' => $p->amenities->map(fn($a) => [
            'id' => $a->id,
            'name' => $a->name,
            'icon' => $a->icon,
        ]),
    ]),

    // Filter options
    'wilayas' => Wilaya::all(), // or cached list
    'communes' => Commune::all(),
    'propertyTypes' => PropertyType::all(),
    'Amenities' => Amenity::all(),

    // Pagination
    'pagination' => [
        'total' => $properties->total(),
        'per_page' => $properties->perPage(),
        'current_page' => $properties->currentPage(),
        'last_page' => $properties->lastPage(),
    ],
]);
```

---

### 2. PropertyController → Property/Show.vue
**Route:** `GET /properties/{id}`
**Layout:** `AppLayout`

**Required Props:**
```php
return Inertia::render('Property/Show', [
    'property' => [
        'id' => $property->id,
        'title' => $property->title,
        'description' => $property->description,
        'wilaya' => $property->wilaya,
        'commune' => $property->commune,
        'address' => $property->address,
        'latitude' => $property->latitude,
        'longitude' => $property->longitude,
        'property_type' => $property->property_type->name ?? null,
        'max_guests' => $property->max_guests,
        'bedrooms' => $property->bedrooms,
        'bathrooms' => $property->bathrooms,
        'surface_area' => $property->surface_area,
        'price_per_night_dzd' => $property->price_per_night_dzd,
        'thumbnail_url' => $property->thumbnail_url,
        'images' => $property->images->map(fn($i) => [
            'id' => $i->id,
            'url' => $i->url,
            'is_primary' => $i->is_primary,
        ]),
        'amenities' => $property->amenities->map(fn($a) => [
            'id' => $a->id,
            'name' => $a->name,
            'icon' => $a->icon,
        ]),
        'average_rating' => $property->average_rating,
        'reviews_count' => $property->reviews_count,
        'owner' => [
            'id' => $property->owner->id,
            'name' => $property->owner->name,
            'email' => $property->owner->email,
            'phone' => $property->owner->phone,
            'response_rate' => $property->owner->response_rate ?? null,
        ],
    ],
    'reviews' => $property->reviews()->latest()->limit(10)->get()->map(fn($r) => [
        'id' => $r->id,
        'rating' => $r->rating,
        'comment' => $r->comment,
        'created_at' => $r->created_at->toIso8601String(),
        'user' => [
            'name' => $r->user->name,
            'avatar' => $r->user->avatar_url ?? null,
        ],
    ]),
    'isFavorited' => $property->isFavoritedBy(auth()->id()),
]);
```

---

### 3. BookingController → Property/Booking.vue
**Route:** `GET /properties/{id}/book`
**Layout:** `AppLayout`

**Required Props:**
```php
return Inertia::render('Property/Booking', [
    'property' => [
        'id' => $property->id,
        'title' => $property->title,
        'wilaya' => $property->wilaya,
        'commune' => $property->commune,
        'price_per_night_dzd' => $property->price_per_night_dzd,
        'thumbnail_url' => $property->thumbnail_url,
        'images' => $property->images->map(fn($i) => ['url' => $i->url]),
    ],
    'checkIn' => $request->input('check_in', now()->addDay()->format('Y-m-d')),
    'checkOut' => $request->input('check_out', now()->addDays(2)->format('Y-m-d')),
    'guests' => (int) $request->input('guests', 1),
    'priceBreakdown' => [
        'price_per_night' => $property->price_per_night_dzd,
        'cleaning_fee' => $property->cleaning_fee ?? 0,
        'service_fee' => $property->service_fee ?? 0,
    ],
    'user' => auth()->check() ? [
        'first_name' => auth()->user()->first_name,
        'last_name' => auth()->user()->last_name,
        'email' => auth()->user()->email,
        'phone' => auth()->user()->phone,
    ] : null,
]);
```

**POST:** `POST /bookings`
- Form submits to this route
- Redirect to: `/client/reservations/{id}` on success

---

### 4. DashboardController → Client/Dashboard.vue
**Route:** `GET /client/dashboard`
**Layout:** `ClientLayout`
**Middleware:** `auth`

**Required Props:**
```php
return Inertia::render('Client/Dashboard', [
    'stats' => [
        'upcoming_trips' => $user->reservations()->upcoming()->count(),
        'completed_trips' => $user->reservations()->completed()->count(),
        'total_spent' => $user->reservations()->completed()->sum('total_price'),
        'favorite_count' => $user->favorites()->count(),
    ],
    'upcomingReservations' => $user->reservations()
        ->upcoming()
        ->with('property')
        ->limit(3)
        ->get()
        ->map(fn($r) => $this->mapReservation($r)),
    'recentReservations' => $user->reservations()
        ->latest()
        ->limit(5)
        ->get()
        ->map(fn($r) => $this->mapReservation($r)),
    'favoriteProperties' => $user->favorites()
        ->with('property')
        ->limit(3)
        ->get()
        ->map(fn($f) => $f->property),
]);
```

---

### 5. ReservationController → Client/Reservations/Index.vue
**Route:** `GET /client/reservations`
**Layout:** `ClientLayout`
**Middleware:** `auth`

**Required Props:**
```php
return Inertia::render('Client/Reservations/Index', [
    'reservations' => $user->reservations()
        ->with('property.owner', 'property.images')
        ->latest()
        ->get()
        ->map(fn($r) => [
            'id' => $r->id,
            'status' => $r->status, // 'pending', 'confirmed', 'completed', 'cancelled'
            'check_in' => $r->check_in->toIso8601String(),
            'check_out' => $r->check_out->toIso8601String(),
            'guests' => $r->guests,
            'total_price' => $r->total_price,
            'price_per_night' => $r->price_per_night,
            'cleaning_fee' => $r->cleaning_fee,
            'service_fee' => $r->service_fee,
            'payment_method' => $r->payment_method,
            'created_at' => $r->created_at->toIso8601String(),
            'property' => [
                'id' => $r->property->id,
                'title' => $r->property->title,
                'wilaya' => $r->property->wilaya,
                'commune' => $r->property->commune,
                'thumbnail_url' => $r->property->thumbnail_url,
                'images' => $r->property->images->map(fn($i) => ['url' => $i->url]),
                'owner' => [
                    'email' => $r->property->owner->email,
                ],
            ],
        ]),
]);
```

---

### 6. ReservationController → Client/Reservations/Show.vue
**Route:** `GET /client/reservations/{id}`
**Layout:** `ClientLayout`
**Middleware:** `auth`

**Required Props:**
```php
return Inertia::render('Client/Reservations/Show', [
    'reservation' => [
        'id' => $reservation->id,
        'status' => $reservation->status,
        'check_in' => $reservation->check_in->toIso8601String(),
        'check_out' => $reservation->check_out->toIso8601String(),
        'guests' => $reservation->guests,
        'total_price' => $reservation->total_price,
        'price_per_night' => $reservation->price_per_night,
        'cleaning_fee' => $reservation->cleaning_fee,
        'service_fee' => $reservation->service_fee,
        'payment_method' => $reservation->payment_method,
        'created_at' => $reservation->created_at->toIso8601String(),
        'property' => [
            'id' => $reservation->property->id,
            'title' => $reservation->property->title,
            'wilaya' => $reservation->property->wilaya,
            'commune' => $reservation->property->commune,
            'address' => $reservation->property->address,
            'thumbnail_url' => $reservation->property->thumbnail_url,
            'images' => $reservation->property->images->map(fn($i) => ['url' => $i->url]),
            'owner' => [
                'name' => $reservation->property->owner->name,
                'email' => $reservation->property->owner->email,
                'phone' => $reservation->property->owner->phone,
                'response_rate' => $reservation->property->owner->response_rate,
            ],
        ],
    ],
    'userReview' => $reservation->review ? [
        'id' => $reservation->review->id,
        'rating' => $reservation->review->rating,
        'comment' => $reservation->review->comment,
        'created_at' => $reservation->review->created_at->toIso8601String(),
        'user' => ['name' => auth()->user()->name],
    ] : null,
]);
```

---

### 7. FavoriteController → Client/Favorites.vue
**Route:** `GET /client/favorites`
**Layout:** `ClientLayout`
**Middleware:** `auth`

**Required Props:**
```php
return Inertia::render('Client/Favorites', [
    'favorites' => $user->favorites()
        ->with('property.images')
        ->latest()
        ->get()
        ->map(fn($f) => [
            'id' => $f->id,
            'created_at' => $f->created_at->toIso8601String(),
            'property' => [
                'id' => $f->property->id,
                'title' => $f->property->title,
                'wilaya' => $f->property->wilaya,
                'commune' => $f->property->commune,
                'property_type' => $f->property->property_type?->name,
                'max_guests' => $f->property->max_guests,
                'price_per_night_dzd' => $f->property->price_per_night_dzd,
                'thumbnail_url' => $f->property->thumbnail_url,
                'images' => $f->property->images->map(fn($i) => ['url' => $i->url]),
                'average_rating' => $f->property->average_rating,
            ],
        ]),
]);
```

---

### 8. ClientProfileController → Client/Profile.vue
**Route:** `GET /client/profile`
**Layout:** `ClientLayout`
**Middleware:** `auth`

**Required Props:**
```php
return Inertia::render('Client/Profile', [
    'user' => [
        'id' => auth()->id(),
        'first_name' => auth()->user()->first_name,
        'last_name' => auth()->user()->last_name,
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
        'phone' => auth()->user()->phone,
        'bio' => auth()->user()->bio,
        'date_of_birth' => auth()->user()->date_of_birth?->format('Y-m-d'),
        'language' => auth()->user()->language ?? 'fr',
        'currency' => auth()->user()->currency ?? 'DZD',
        'email_notifications' => auth()->user()->email_notifications ?? true,
        'promotional_emails' => auth()->user()->promotional_emails ?? false,
    ],
]);
```

**PUT:** `PUT /client/profile` - Update profile
**PUT:** `PUT /client/password` - Update password
**PUT:** `PUT /client/preferences` - Update preferences

---

## Helper: Reservation Mapper

For consistent reservation data across pages, use this mapper:

```php
private function mapReservation($reservation)
{
    return [
        'id' => $reservation->id,
        'status' => $reservation->status,
        'check_in' => $reservation->check_in->toIso8601String(),
        'check_out' => $reservation->check_out->toIso8601String(),
        'guests' => $reservation->guests,
        'total_price' => $reservation->total_price,
        'price_per_night' => $reservation->price_per_night,
        'cleaning_fee' => $reservation->cleaning_fee,
        'service_fee' => $reservation->service_fee,
        'payment_method' => $reservation->payment_method,
        'created_at' => $reservation->created_at->toIso8601String(),
        'property' => [
            'id' => $reservation->property->id,
            'title' => $reservation->property->title,
            'wilaya' => $reservation->property->wilaya,
            'commune' => $reservation->property->commune,
            'thumbnail_url' => $reservation->property->thumbnail_url,
            'images' => $reservation->property->images->map(fn($i) => [
                'url' => $i->url,
            ]),
            'owner' => [
                'name' => $reservation->property->owner->name,
                'email' => $reservation->property->owner->email,
                'phone' => $reservation->property->owner->phone,
            ],
        ],
    ];
}
```

---

## Shared Props (Auth & Global)

All pages receive `auth` from HandleInertiaRequests middleware:

```php
// In app/Http/Middleware/HandleInertiaRequests.php
array_merge(parent::share($request), [
    'auth' => [
        'user' => fn() => auth::check() ? [
            'id' => auth()->id(),
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ] : null,
    ],
    'locale' => fn() => session('locale', 'fr'),
    'browser' => fn() => Agent::browser(),
    'platform' => fn() => Agent::platform(),
]);
```

---

## Status Codes & Errors

- **200** - Success, render Inertia page with props
- **302** - Redirect (login, etc.)
- **422** - Validation errors (Inertia `onError` callback)

Validation errors are auto-handled by Inertia forms:

```javascript
form.post('/route', {
    onError: (errors) => {
        // errors.first_name, etc.
    }
});
```

---

## API Calls Made from Frontend

These are called directly via fetch() (not via web controllers):

| Endpoint | Method | Used By |
|----------|--------|---------|
| `/api/properties/{id}/favorite` | POST/DELETE | PropertyCard, Favorites |
| `/api/properties/{id}/reviews` | POST | ReviewForm |
| `/api/v1/properties/{id}/availability` | GET | BookingWidget |
| `/api/v1/client/favorites` | GET | Favorites (fallback) |

---

## Notes for Backend Developers

1. **Date Format:** Always return dates in ISO 8601 format (`->toIso8601String()`)
2. **Pagination:** Use Laravel's paginator directly, map to simple array
3. **Images:** Always include a fallback for missing images
4. **Null Safety:** Use `??` operator for optional relations
5. **Authorization:** Controllers should verify ownership before returning data
6. **Status Values:** Use exact strings: `pending`, `confirmed`, `completed`, `cancelled`

---

## Testing Checklist

- [ ] Search loads with empty filters
- [ ] Property detail page loads with reviews
- [ ] Booking page calculates prices correctly
- [ ] Dashboard shows accurate stats
- [ ] Reservations list filters by status
- [ ] Reservation detail shows cancellation policy
- [ ] Favorites list can remove items
- [ ] Profile updates persist
- [ ] Password change works
- [ ] Preferences save correctly
