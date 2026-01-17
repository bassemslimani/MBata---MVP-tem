# Mbata V2 MVP - Master Plan
**Last Updated:** 2025-01-16
**Status:** Sprint 8 - Payment System

---

## Legend
- `[TODO]` - Not started
- `[IN PROGRESS]` - Currently being worked on
- `[DONE]` - Completed
- `[BLOCKED]` - Waiting for dependencies

---

## PHASE 1: FOUNDATION & LOCALIZATION

### 1.1 Database Schema - Localization & Users

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 1.1.1 | `users` | id, name, email, phone, email_verified_at, password, role (enum: super_admin, admin, owner, client), locale (fr, ar, en), avatar, remember_token | HasMany: properties, reservations, reviews, payment_proofs | [DONE] |
| 1.1.2 | `wilayas` | id, code (unique), name_fr, name_ar, name_en, longitude, latitude | HasMany: communes, properties | [DONE] |
| 1.1.3 | `communes` | id, wilaya_id, code (unique), name_fr, name_ar, name_en, postal_code | BelongsTo: wilaya, HasMany: properties | [DONE] |
| 1.1.4 | `currencies` | id, code (DZD, EUR, USD), name_fr, name_ar, symbol, exchange_rate_to_dzd, is_default | - | [DONE] |
| 1.1.5 | `settings` | id, key, value_fr, value_ar, value_en, type (text, number, boolean), group | - | [DONE] |

### 1.2 Backend API - Localization & Auth

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 1.2.1 | `/api/wilayas` | GET | WilayaController | List all wilayas | [DONE] |
| 1.2.2 | `/api/wilayas/{id}/communes` | GET | WilayaController | Get communes by wilaya | [DONE] |
| 1.2.3 | `/api/currencies/exchange-rates` | GET | CurrencyController | Get current exchange rates | [DONE] |
| 1.2.4 | `/api/auth/register` | POST | AuthController | User registration (email/phone) | [TODO] |
| 1.2.5 | `/api/auth/login` | POST | AuthController | User login | [TODO] |
| 1.2.6 | `/api/auth/logout` | POST | AuthController | User logout | [TODO] |
| 1.2.7 | `/api/auth/verify-email` | POST | AuthController | Email verification | [TODO] |
| 1.2.8 | `/api/auth/verify-phone` | POST | AuthController | Phone verification (SMS) | [TODO] |
| 1.2.9 | `/api/auth/forgot-password` | POST | AuthController | Password reset request | [TODO] |
| 1.2.10 | `/api/auth/reset-password` | POST | AuthController | Password reset | [TODO] |

### 1.3 Frontend Pages - Auth & Layout

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 1.3.1 | `Layout/AppLayout.vue` | - | Main layout with header, footer, language selector | [DONE] |
| 1.3.2 | `Layout/AdminLayout.vue` | - | Admin dashboard layout | [DONE] |
| 1.3.3 | `Pages/Auth/Register.vue` | /register | Registration form (email/phone) | [DONE] |
| 1.3.4 | `Pages/Auth/Login.vue` | /login | Login form | [DONE] |
| 1.3.5 | `Pages/Auth/ForgotPassword.vue` | /forgot-password | Password reset request | [DONE] |
| 1.3.6 | `Components/LanguageSelector.vue` | - | Dropdown to switch FR/AR/EN | [DONE] |
| 1.3.7 | `Pages/Home.vue` | / | Homepage with hero section and featured properties | [DONE] |

---

## PHASE 2: PROPERTY MANAGEMENT (OWNER)

### 2.1 Database Schema - Properties

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 2.1.1 | `property_types` | id, name_fr, name_ar, name_en, icon, is_active | HasMany: properties | [DONE] |
| 2.1.2 | `property_categories` | id, name_fr, name_ar, name_en, description_fr, description_ar, description_en, is_active | HasMany: properties | [DONE] |
| 2.1.3 | `amenities` | id, name_fr, name_ar, name_en, icon, is_active | BelongsToMany: properties | [DONE] |
| 2.1.4 | `properties` | id, user_id (owner), wilaya_id, commune_id, property_type_id, property_category_id, title_fr, title_ar, title_en, description_fr, description_ar, description_en, address, latitude, longitude, price_per_night_dzd, surface_area, rooms, bedrooms, bathrooms, max_guests, is_active, is_verified, views_count | BelongsTo: user, wilaya, commune, property_type, property_category; BelongsToMany: amenities; HasMany: reservations, reviews, property_images, availabilities | [DONE] |
| 2.1.5 | `property_images` | id, property_id, image_path, is_primary, order, alt_text_fr, alt_text_ar, alt_text_en | BelongsTo: property | [DONE] |
| 2.1.6 | `availabilities` | id, property_id, start_date, end_date, is_available, price_override_dzd, notes | BelongsTo: property | [DONE] |

### 2.2 Backend API - Properties

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 2.2.1 | `/api/property-types` | GET | PropertyTypeController | List all property types | [DONE] |
| 2.2.2 | `/api/property-categories` | GET | PropertyCategoryController | List all categories | [DONE] |
| 2.2.3 | `/api/amenities` | GET | AmenityController | List all amenities | [DONE] |
| 2.2.4 | `/api/owner/properties` | GET | OwnerPropertyController | List owner's properties | [DONE] |
| 2.2.5 | `/api/owner/properties` | POST | OwnerPropertyController | Create new property | [DONE] |
| 2.2.6 | `/api/owner/properties/{id}` | GET | OwnerPropertyController | Get property details | [DONE] |
| 2.2.7 | `/api/owner/properties/{id}` | PUT/PATCH | OwnerPropertyController | Update property | [DONE] |
| 2.2.8 | `/api/owner/properties/{id}` | DELETE | OwnerPropertyController | Delete property | [DONE] |
| 2.2.9 | `/api/owner/properties/{id}/images` | POST | OwnerPropertyController | Upload property images | [DONE] |
| 2.2.10 | `/api/owner/properties/{id}/images/{imageId}` | DELETE | OwnerPropertyController | Delete image | [DONE] |
| 2.2.11 | `/api/owner/properties/{id}/images/{imageId}/primary` | PUT | OwnerPropertyController | Set primary image | [DONE] |
| 2.2.12 | `/api/owner/properties/{id}/availabilities` | GET | AvailabilityController | Get property availabilities | [DONE] |
| 2.2.13 | `/api/owner/properties/{id}/availabilities` | POST | AvailabilityController | Set availability range | [DONE] |
| 2.2.14 | `/api/owner/properties/{id}/availabilities/{id}` | DELETE | AvailabilityController | Remove availability | [DONE] |

### 2.3 Frontend Pages - Owner Dashboard

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 2.3.1 | `Layout/OwnerLayout.vue` | - | Owner dashboard layout | [DONE] |
| 2.3.2 | `Pages/Owner/Dashboard.vue` | /owner/dashboard | Owner dashboard with stats | [DONE] |
| 2.3.3 | `Pages/Owner/Properties/Index.vue` | /owner/properties | List of owner's properties | [DONE] |
| 2.3.4 | `Pages/Owner/Properties/Create.vue` | /owner/properties/create | Create new property form | [DONE] |
| 2.3.5 | `Pages/Owner/Properties/Edit.vue` | /owner/properties/{id}/edit | Edit property form | [TODO] |
| 2.3.6 | `Pages/Owner/Properties/Photos.vue` | /owner/properties/{id}/photos | Photo gallery manager | [DONE] |
| 2.3.7 | `Pages/Owner/Properties/Availabilities.vue` | /owner/properties/{id}/availabilities | Availability calendar | [DONE] |
| 2.3.8 | `Components/Property/PropertyCard.vue` | - | Property card component | [DONE] |
| 2.3.9 | `Components/Property/ImageUploader.vue` | - | Drag-drop image upload | [DONE] |
| 2.3.10 | `Components/Property/AmenitySelector.vue` | - | Amenity checkboxes | [DONE] |
| 2.3.11 | `Components/Forms/WilayaCommuneSelect.vue` | - | Cascading wilaya/commune dropdown | [DONE] |
| 2.3.12 | `Components/Forms/PriceInput.vue` | - | Price input with currency preview | [DONE] |
| 2.3.13 | `Components/Property/AvailabilityCalendar.vue` | - | Date range calendar | [DONE] |

---

## PHASE 3: PROPERTY SEARCH & BOOKING (CLIENT)

### 3.1 Database Schema - Reservations & Reviews

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 3.1.1 | `reservations` | id, property_id, client_user_id, check_in_date, check_out_date, guests_count, total_price_dzd, currency_code, status (pending, confirmed, cancelled, completed), special_requests, created_at | BelongsTo: property, client; HasOne: payment | [DONE] |
| 3.1.2 | `reviews` | id, property_id, reservation_id, user_id (client), rating (1-5), comment_fr, comment_ar, is_visible | BelongsTo: property, reservation, user | [DONE] |
| 3.1.3 | `favorites` | id, user_id, property_id | BelongsTo: user, property | [DONE] |

### 3.2 Backend API - Search & Booking

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 3.2.1 | `/api/properties` | GET | PropertyController | Search properties with filters | [DONE] |
| 3.2.2 | `/api/properties/{id}` | GET | PropertyController | Get property details + reviews | [DONE] |
| 3.2.3 | `/api/properties/{id}/reviews` | GET | PropertyController | Get property reviews | [DONE] |
| 3.2.4 | `/api/properties/{id}/availability` | GET | PropertyController | Check availability for dates | [DONE] |
| 3.2.5 | `/api/client/reservations` | POST | ReservationController | Create reservation request | [DONE] |
| 3.2.6 | `/api/client/reservations` | GET | ReservationController | List client's reservations | [DONE] |
| 3.2.7 | `/api/client/reservations/{id}` | GET | ReservationController | Get reservation details | [DONE] |
| 3.2.8 | `/api/client/reservations/{id}/cancel` | POST | ReservationController | Cancel reservation | [DONE] |
| 3.2.9 | `/api/client/favorites` | GET | FavoriteController | List favorites | [DONE] |
| 3.2.10 | `/api/client/favorites` | POST | FavoriteController | Add to favorites | [DONE] |
| 3.2.11 | `/api/client/favorites/{id}` | DELETE | FavoriteController | Remove from favorites | [DONE] |
| 3.2.12 | `/api/client/reviews` | POST | ReviewController | Submit review | [DONE] |

### 3.3 Frontend Pages - Client Experience

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 3.3.1 | `Pages/Search.vue` | /search | Property search with filters | [DONE] |
| 3.3.2 | `Pages/Property/Show.vue` | /properties/{id} | Property detail page | [DONE] |
| 3.3.3 | `Pages/Property/Booking.vue` | /properties/{id}/book | Booking form & payment | [DONE] |
| 3.3.4 | `Pages/Client/Dashboard.vue` | /client/dashboard | Client dashboard | [DONE] |
| 3.3.5 | `Pages/Client/Reservations/Index.vue` | /client/reservations | My reservations list | [DONE] |
| 3.3.6 | `Pages/Client/Reservations/Show.vue` | /client/reservations/{id} | Reservation details | [DONE] |
| 3.3.7 | `Pages/Client/Favorites.vue` | /client/favorites | Saved properties | [DONE] |
| 3.3.8 | `Pages/Client/Profile.vue` | /client/profile | Profile settings | [DONE] |
| 3.3.9 | `Components/Booking/BookingWidget.vue` | - | Booking widget with dates/guests | [DONE] |
| 3.3.10 | `Components/Booking/DateRangePicker.vue` | - | Check-in/out date picker | [DONE] |
| 3.3.11 | `Components/Booking/GuestSelector.vue` | - | Guest count selector | [DONE] |
| 3.3.12 | `Components/Booking/PriceSummary.vue` | - | Booking price breakdown | [DONE] |
| 3.3.13 | `Components/Property/PropertyGallery.vue` | - | Image gallery with lightbox | [DONE] |
| 3.3.14 | `Components/Property/AmenitiesList.vue` | - | Amenity icons with labels | [DONE] |
| 3.3.15 | `Components/Review/ReviewCard.vue` | - | Review display with rating | [DONE] |
| 3.3.16 | `Components/Review/ReviewForm.vue` | - | Review submission form | [DONE] |

---

## PHASE 4: PAYMENT SYSTEM

### 4.1 Database Schema - Payments

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 4.1.1 | `payments` | id, reservation_id, amount_dzd, currency_code, payment_method (baridi_ccp, stripe), status (pending, completed, failed, refused), stripe_payment_id, payment_proof_path, admin_notes, verified_at, verified_by | BelongsTo: reservation, admin (verified_by) | [TODO] |

### 4.2 Backend API - Payments

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 4.2.1 | `/api/client/reservations/{id}/payment/baridi` | POST | PaymentController | Initiate Baridi CCP payment | [TODO] |
| 4.2.2 | `/api/client/reservations/{id}/payment/stripe` | POST | PaymentController | Initiate Stripe payment | [TODO] |
| 4.2.3 | `/api/payment/stripe/webhook` | POST | StripeWebhookController | Stripe webhook handler | [TODO] |
| 4.2.4 | `/api/admin/payments` | GET | AdminPaymentController | List all payments | [TODO] |
| 4.2.5 | `/api/admin/payments/{id}` | GET | AdminPaymentController | Get payment details | [TODO] |
| 4.2.6 | `/api/admin/payments/{id}/verify` | POST | AdminPaymentController | Verify Baridi CCP payment | [TODO] |
| 4.2.7 | `/api/admin/payments/{id}/reject` | POST | AdminPaymentController | Reject payment | [TODO] |
| 4.2.8 | `/api/admin/exchange-rates` | PUT | AdminCurrencyController | Update exchange rates | [TODO] |

### 4.3 Frontend Pages - Payment

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 4.3.1 | `Pages/Payment/Baridi.vue` | /payment/baridi/{reservationId} | Baridi CCP payment instructions | [TODO] |
| 4.3.2 | `Pages/Payment/Stripe.vue` | /payment/stripe/{reservationId} | Stripe checkout | [TODO] |
| 4.3.3 | `Pages/Payment/Success.vue` | /payment/success | Payment confirmation | [TODO] |
| 4.3.4 | `Pages/Payment/Failed.vue` | /payment/failed | Payment failed | [TODO] |
| 4.3.5 | `Components/Payment/BaridiInfo.vue` | - | Display Baridi CCP info | [TODO] |
| 4.3.6 | `Components/Payment/ProofUploader.vue` | - | Upload payment proof | [TODO] |
| 4.3.7 | `Pages/Admin/Payments/Index.vue` | /admin/payments | Payment verification list | [TODO] |

---

## PHASE 5: ADMINISTRATION

### 5.1 Database Schema - Admin

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 5.1.1 | `admin_actions` | id, admin_id, action_type, entity_type, entity_id, old_values, new_values, ip_address, user_agent | BelongsTo: admin (user) | [DONE] |

### 5.2 Backend API - Admin

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 5.2.1 | `/api/admin/dashboard` | GET | AdminDashboardController | Dashboard stats | [DONE] |
| 5.2.2 | `/api/admin/users` | GET | AdminUserController | List all users | [DONE] |
| 5.2.3 | `/api/admin/users/{id}` | GET | AdminUserController | User details | [DONE] |
| 5.2.4 | `/api/admin/users/{id}/toggle-status` | POST | AdminUserController | Activate/suspend user | [DONE] |
| 5.2.5 | `/api/admin/properties` | GET | AdminPropertyController | List all properties | [DONE] |
| 5.2.6 | `/api/admin/properties/{id}/verify` | POST | AdminPropertyController | Verify property | [DONE] |
| 5.2.7 | `/api/admin/properties/{id}` | DELETE | AdminPropertyController | Delete property | [DONE] |
| 5.2.8 | `/api/admin/reservations` | GET | AdminReservationController | List all reservations | [DONE] |
| 5.2.9 | `/api/admin/reviews` | GET | AdminReviewController | Moderate reviews | [DONE] |
| 5.2.10 | `/api/admin/reviews/{id}/toggle-visibility` | POST | AdminReviewController | Show/hide review | [DONE] |
| 5.2.11 | `/api/super-admin/admins` | GET | SuperAdminController | Manage admins | [DONE] |
| 5.2.12 | `/api/super-admin/admins` | POST | SuperAdminController | Create admin | [DONE] |
| 5.2.13 | `/api/super-admin/admins/{id}` | DELETE | SuperAdminController | Remove admin | [DONE] |

### 5.3 Frontend Pages - Admin

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 5.3.1 | `Pages/Admin/Dashboard.vue` | /admin/dashboard | Admin dashboard with stats | [DONE] |
| 5.3.2 | `Pages/Admin/Users.vue` | /admin/users | User management | [DONE] |
| 5.3.3 | `Pages/Admin/Properties.vue` | /admin/properties | Property moderation | [DONE] |
| 5.3.4 | `Pages/Admin/Reservations.vue` | /admin/reservations | All reservations | [DONE] |
| 5.3.5 | `Pages/Admin/Reviews.vue` | /admin/reviews | Review moderation | [DONE] |
| 5.3.6 | `Pages/Admin/Settings/Currency.vue` | /admin/settings/currency | Exchange rate settings | [DONE] |
| 5.3.7 | `Pages/SuperAdmin/Admins.vue` | /super-admin/admins | Admin management | [DONE] |

---

## PHASE 6: REAL ESTATE PROMOTIONS

### 6.1 Database Schema - Promotions

| ID | Table | Columns | Relationships | Status |
|----|-------|---------|---------------|--------|
| 6.1.1 | `promotions` | id, admin_id, title_fr, title_ar, title_en, description_fr, description_ar, description_en, price_dzd, property_type, wilaya_id, commune_id, address, latitude, longitude, surface_area, rooms, bedrooms, is_published, published_at, expires_at | BelongsTo: admin, wilaya, commune; HasMany: promotion_images, promotion_leads | [TODO] |
| 6.1.2 | `promotion_images` | id, promotion_id, image_path, order, alt_text | BelongsTo: promotion | [TODO] |
| 6.1.3 | `promotion_leads` | id, promotion_id, name, email, phone, message, is_contacted | BelongsTo: promotion | [TODO] |

### 6.2 Backend API - Promotions

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 6.2.1 | `/api/promotions` | GET | PromotionController | List published promotions | [TODO] |
| 6.2.2 | `/api/promotions/{id}` | GET | PromotionController | Promotion details | [TODO] |
| 6.2.3 | `/api/promotions/{id}/contact` | POST | PromotionController | Submit contact form | [TODO] |
| 6.2.4 | `/api/admin/promotions` | GET | AdminPromotionController | Manage promotions | [TODO] |
| 6.2.5 | `/api/admin/promotions` | POST | AdminPromotionController | Create promotion | [TODO] |
| 6.2.6 | `/api/admin/promotions/{id}` | PUT/PATCH | AdminPromotionController | Update promotion | [TODO] |
| 6.2.7 | `/api/admin/promotions/{id}` | DELETE | AdminPromotionController | Delete promotion | [TODO] |
| 6.2.8 | `/api/admin/promotions/{id}/leads` | GET | AdminPromotionController | Get leads | [TODO] |

### 6.3 Frontend Pages - Promotions

| ID | Page/Component | Route | Description | Status |
|----|----------------|-------|-------------|--------|
| 6.3.1 | `Pages/Promotions/Index.vue` | /promotions | Real estate promotions list | [TODO] |
| 6.3.2 | `Pages/Promotions/Show.vue` | /promotions/{id} | Promotion detail + contact | [TODO] |
| 6.3.3 | `Pages/Admin/Promotions/Index.vue` | /admin/promotions | Manage promotions | [TODO] |
| 6.3.4 | `Pages/Admin/Promotions/Create.vue` | /admin/promotions/create | Create promotion | [TODO] |
| 6.3.5 | `Components/Promotion/PromotionCard.vue` | - | Promotion card | [TODO] |

---

## PHASE 7: MULTILINGUAL & PWA

### 7.1 Backend API - Translations

| ID | Endpoint | Method | Controller | Description | Status |
|----|----------|--------|------------|-------------|--------|
| 7.1.1 | `/api/translations` | GET | TranslationController | Get all translations for locale | [TODO] |
| 7.2.2 | `/api/admin/translations` | GET | AdminTranslationController | Manage translations | [TODO] |
| 7.2.3 | `/api/admin/translations` | POST | AdminTranslationController | Create/update translation | [TODO] |

### 7.2 Frontend - PWA & i18n

| ID | Component/Config | Description | Status |
|----|------------------|-------------|--------|
| 7.2.1 | `resources/js/i18n/fr.json` | French translations | [DONE] |
| 7.2.2 | `resources/js/i18n/ar.json` | Arabic translations (RTL) | [DONE] |
| 7.2.3 | `resources/js/i18n/en.json` | English translations | [DONE] |
| 7.2.4 | `public/manifest.json` | PWA manifest | [TODO] |
| 7.2.5 | `public/sw.js` | Service worker | [TODO] |
| 7.2.6 | `resources/js/pwa/register-sw.js` | Service worker registration | [TODO] |
| 7.2.7 | RTL support in Tailwind config | `rtl: true` variant | [DONE] |
| 7.2.8 | Install prompt component | PWA install banner | [TODO] |

---

## Sprint Progress Summary

| Sprint | Focus | Backend | Frontend | QA | Status |
|--------|-------|---------|----------|-----|--------|
| 1 | Foundation & Localization | 3/10 | 7/7 | - | [DONE] |
| 2 | Properties (Owner) | 20/20 | 12/13 | - | [DONE] |
| 3 | Search & Booking (Client) | 12/12 | 16/16 | - | [DONE] |
| 4 | Client Experience Frontend | 1/1 | 16/16 | - | [DONE] |
| 5 | Web Controllers Integration | 7/7 | - | - | [DONE] |
| 6 | Administration Backend | 13/13 | - | - | [DONE] |
| 7 | Administration Frontend | 7/7 | 7/7 | - | [DONE] |
| 8 | Payment System | 0/8 | 0/7 | - | [STARTING] |
| 9 | Real Estate Promotions | 0/8 | 0/5 | - | [TODO] |
| 10 | Multilingual & PWA | 0/3 | 4/8 | - | [TODO] |

---

## Notes
- All dates stored in UTC
- All prices stored in DZD internally
- Images stored in `storage/app/public/{directory}`
- Use Laravel policies for authorization
- Use form requests for validation
- All API responses follow consistent JSON format
