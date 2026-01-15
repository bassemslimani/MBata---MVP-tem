# Backend Memory - Mbata V2 MVP
**Last Updated:** 2025-01-15

## Progress Status

### PHASE 1: FOUNDATION & LOCALIZATION - Backend [COMPLETED]

| ID | Task | Status |
|----|------|--------|
| 1.1.1 | `users` table - Added phone, role (enum), locale, avatar | DONE |
| 1.1.2 | `wilayas` table - Model + Migration created | DONE |
| 1.1.3 | `communes` table - Model + Migration created | DONE |
| 1.1.4 | `currencies` table - Model + Migration created | DONE |
| 1.1.5 | `settings` table - Model + Migration created | DONE |
| 1.2.1 | `/api/wilayas` - GET endpoint created | DONE |
| 1.2.2 | `/api/wilayas/{id}/communes` - GET endpoint created | DONE |
| 1.2.3 | `/api/currencies/exchange-rates` - GET endpoint created | DONE |

### PHASE 2: PROPERTY MANAGEMENT (OWNER) - Backend [COMPLETED]

| ID | Task | Status |
|----|------|--------|
| 2.1.1 | `property_types` table - Model + Migration created | DONE |
| 2.1.2 | `property_categories` table - Model + Migration created | DONE |
| 2.1.3 | `amenities` table + pivot table - Model + Migration created | DONE |
| 2.1.4 | `properties` table - Model + Migration created | DONE |
| 2.1.5 | `property_images` table - Model + Migration created | DONE |
| 2.1.6 | `availabilities` table - Model + Migration created | DONE |
| 2.2.1-2.2.14 | All owner property endpoints | DONE |

### PHASE 3: PROPERTY SEARCH & BOOKING (CLIENT) - Backend [COMPLETED]

| ID | Task | Status |
|----|------|--------|
| 3.1.1 | `reservations` table - Model + Migration created | DONE |
| 3.1.2 | `reviews` table - Model + Migration created | DONE |
| 3.1.3 | `favorites` table - Model + Migration created | DONE |
| 3.2.1 | `/api/properties` - GET PropertyController (search with filters) | DONE |
| 3.2.2 | `/api/properties/{id}` - GET PropertyController (details) | DONE |
| 3.2.3 | `/api/properties/{id}/reviews` - GET PropertyController (reviews) | DONE |
| 3.2.4 | `/api/properties/{id}/availability` - GET PropertyController (check availability) | DONE |
| 3.2.5 | `/api/client/reservations` - POST ReservationController (create) | DONE |
| 3.2.6 | `/api/client/reservations` - GET ReservationController (list) | DONE |
| 3.2.7 | `/api/client/reservations/{id}` - GET ReservationController (details) | DONE |
| 3.2.8 | `/api/client/reservations/{id}/cancel` - POST ReservationController (cancel) | DONE |
| 3.2.9 | `/api/client/favorites` - GET FavoriteController (list) | DONE |
| 3.2.10 | `/api/client/favorites` - POST FavoriteController (add) | DONE |
| 3.2.11 | `/api/client/favorites/{id}` - DELETE FavoriteController (remove) | DONE |
| 3.2.12 | `/api/client/reviews` - POST ReviewController (submit) | DONE |

### Files Created/Modified:

#### Database Migrations (13 total)
- `0001_01_01_000000_create_users_table.php` - Modified (phone, role, locale, avatar)
- `2025_01_15_000001_create_wilayas_table.php`
- `2025_01_15_000002_create_communes_table.php`
- `2025_01_15_000003_create_currencies_table.php`
- `2025_01_15_000004_create_settings_table.php`
- `2025_01_15_000005_create_property_types_table.php`
- `2025_01_15_000006_create_property_categories_table.php`
- `2025_01_15_000007_create_amenities_table.php` (with pivot)
- `2025_01_15_000008_create_properties_table.php`
- `2025_01_15_000009_create_property_images_table.php`
- `2025_01_15_000010_create_availabilities_table.php`
- `2025_01_15_000011_create_reservations_table.php`
- `2025_01_15_000012_create_reviews_table.php`
- `2025_01_15_000013_create_favorites_table.php`

#### Models (20 total)
- `User.php` - Updated with favorites relationship
- `Wilaya.php`, `Commune.php`, `Currency.php`, `Setting.php`
- `Property.php` - Scopes: active(), verified(), byOwner(), availableBetween(), withAmenities(), favoritedBy()
- `PropertyType.php`, `PropertyCategory.php`, `Amenity.php`
- `PropertyImage.php`, `Availability.php`
- `Reservation.php` - Scopes: pending(), confirmed(), active(), byClient(), byProperty(), overlapping()
- `Review.php` - Scopes: visible(), byProperty(), byUser()
- `Favorite.php` - Scope: byUser()
- `Payment.php`, `PaymentProof.php` (placeholders for Phase 4)

#### API Resources (11 total)
- `PropertyTypeResource.php`, `PropertyCategoryResource.php`, `AmenityResource.php`
- `PropertyResource.php` (owner use)
- `PropertySearchResource.php` (search listing)
- `PropertyDetailResource.php` (full details)
- `PropertyImageResource.php`, `AvailabilityResource.php`
- `ReviewResource.php`, `ReservationResource.php`, `FavoriteResource.php`

#### Form Requests (9 total)
- `StorePropertyRequest.php`, `UpdatePropertyRequest.php`
- `StorePropertyImageRequest.php`, `StoreAvailabilityRequest.php`, `UpdateAvailabilityRequest.php`
- `SearchPropertiesRequest.php`, `StoreReservationRequest.php`, `StoreReviewRequest.php`
- `CheckAvailabilityRequest.php`

#### Controllers (11 total)
- `WilayaController.php`, `CurrencyController.php`
- `PropertyTypeController.php`, `PropertyCategoryController.php`, `AmenityController.php`
- `Owner/OwnerPropertyController.php`, `Owner/AvailabilityController.php`
- `PropertyController.php` - index (search), show, reviews, availability
- `Client/ReservationController.php` - index, store, show, cancel
- `Client/ReviewController.php` - index, store, show
- `Client/FavoriteController.php` - index, store, destroy

#### Routes
- `routes/api.php` - All v1 endpoints configured (public, client, owner)

#### Seeders (6)
- `WilayaSeeder.php` - 58 wilayas
- `CommuneSeeder.php` - Sample communes
- `PropertyTypeSeeder.php` - 8 types
- `PropertyCategorySeeder.php` - 5 categories
- `AmenitySeeder.php` - 20 amenities
- `DatabaseSeeder.php` - Updated

## Next Tasks:

### PHASE 1 (remaining - Auth):
- [ ] 1.2.4 - `/api/auth/register` - POST AuthController
- [ ] 1.2.5 - `/api/auth/login` - POST AuthController
- [ ] 1.2.6 - `/api/auth/logout` - POST AuthController
- [ ] 1.2.7 - `/api/auth/verify-email` - POST AuthController
- [ ] 1.2.8 - `/api/auth/verify-phone` - POST AuthController
- [ ] 1.2.9 - `/api/auth/forgot-password` - POST AuthController
- [ ] 1.2.10 - `/api/auth/reset-password` - POST AuthController

### PHASE 4:
- [ ] 4.1.1 - `payments` table
- [ ] 4.2.x - Payment API endpoints (Baridi CCP, Stripe, Admin verification)

### PHASE 5:
- [ ] 5.1.1 - `admin_actions` table
- [ ] 5.2.x - Admin dashboard, users, properties, reservations, reviews endpoints

### PHASE 6:
- [ ] 6.1.x - Promotions tables
- [ ] 6.2.x - Promotions API endpoints

## Notes:
- All API routes prefixed with `/api/v1/`
- Localization via `Accept-Language: fr|ar|en` header
- Client/Owner endpoints require `auth:sanctum` middleware
- Images stored in `storage/app/public/properties/{id}/`
- Soft deletes enabled on Property model
- Double-booking prevention via overlapping reservation check
- Total price calculation with availability overrides supported
