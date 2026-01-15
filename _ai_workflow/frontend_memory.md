# Frontend Memory - Mbata V2 MVP
**Last Updated:** 2025-01-15

## Project Context
- Laravel 12 + Vue 3 + Inertia.js
- Tailwind CSS with @tailwindcss/forms
- Composition API with `<script setup>`
- Multi-language support: FR (default), AR (RTL), EN
- Primary color: Orange (orange-500)

## Current Progress

### Phase 1: Foundation & Localization (Frontend)
| ID | Component/Page | Status | Notes |
|----|----------------|--------|-------|
| 1.3.1 | Layout/AppLayout.vue | DONE | Main layout with RTL support |
| 1.3.2 | Layout/AdminLayout.vue | DONE | Admin layout with sidebar |
| 1.3.3 | Pages/Auth/Register.vue | EXISTS | May need updates |
| 1.3.4 | Pages/Auth/Login.vue | EXISTS | May need updates |
| 1.3.5 | Pages/Auth/ForgotPassword.vue | EXISTS | May need updates |
| 1.3.6 | Components/Layout/LanguageSelector.vue | DONE | FR/AR/EN dropdown |
| 1.3.7 | Pages/Home.vue | DONE | Hero + featured properties |

### Phase 2: Property Management (Owner)
| ID | Component/Page | Status | Notes |
|----|----------------|--------|-------|
| 2.3.1 | Layout/OwnerLayout.vue | DONE | Owner dashboard layout |
| 2.3.2 | Pages/Owner/Dashboard.vue | DONE | Stats + quick actions |
| 2.3.3 | Pages/Owner/Properties/Index.vue | DONE | List with filters, status toggle |
| 2.3.4 | Pages/Owner/Properties/Create.vue | DONE | Multi-step form (5 steps) |
| 2.3.5 | Pages/Owner/Properties/Edit.vue | TODO | Reuses Create form structure |
| 2.3.6 | Pages/Owner/Properties/Photos.vue | DONE | Image management |
| 2.3.7 | Pages/Owner/Properties/Availabilities.vue | DONE | Calendar view |
| 2.3.8 | Components/Property/PropertyCard.vue | DONE | Reusable property card |
| 2.3.9 | Components/Property/ImageUploader.vue | DONE | Drag-drop with progress |
| 2.3.10 | Components/Property/AmenitySelector.vue | DONE | Checkbox grid with icons |
| 2.3.11 | Components/Forms/WilayaCommuneSelect.vue | DONE | Cascading dropdown |
| 2.3.12 | Components/Forms/PriceInput.vue | DONE | DZD with EUR/USD preview |
| 2.3.13 | Components/Property/AvailabilityCalendar.vue | DONE | Date range calendar |

### Phase 3: Property Search & Booking (Client)
| ID | Component/Page | Status | Notes |
|----|----------------|--------|-------|
| 3.3.1 | Pages/Search.vue | DONE | Filters sidebar + grid |

### Phase 5: Administration
| ID | Component/Page | Status | Notes |
|----|----------------|--------|-------|
| 5.3.1 | Pages/Admin/Dashboard.vue | DONE | Stats + pending items |

### Phase 7: Multilingual & PWA
| ID | Component/File | Status | Notes |
|----|----------------|--------|-------|
| 7.2.1 | resources/js/i18n/fr.json | DONE | French + owner properties |
| 7.2.2 | resources/js/i18n/ar.json | DONE | Arabic (RTL) + owner properties |
| 7.2.3 | resources/js/i18n/en.json | DONE | English + owner properties |
| 7.2.7 | RTL support | DONE | Via :dir binding |

## Component Structure
```
resources/js/
├── Components/
│   ├── [Existing Breeze components]
│   ├── Layout/
│   │   ├── AppHeader.vue       (DONE)
│   │   ├── AppFooter.vue       (DONE)
│   │   └── LanguageSelector.vue  (DONE)
│   ├── Property/
│   │   ├── PropertyCard.vue    (DONE)
│   │   ├── ImageUploader.vue   (DONE)
│   │   ├── AmenitySelector.vue (DONE)
│   │   └── AvailabilityCalendar.vue (DONE)
│   └── Forms/
│       ├── WilayaCommuneSelect.vue (DONE)
│       └── PriceInput.vue      (DONE)
├── Layouts/
│   ├── AppLayout.vue           (DONE)
│   ├── AdminLayout.vue         (DONE)
│   ├── OwnerLayout.vue         (DONE)
│   └── [Existing: AuthenticatedLayout, GuestLayout]
├── Pages/
│   ├── Home.vue                (DONE)
│   ├── Search.vue              (DONE)
│   ├── Auth/                   (EXISTS)
│   ├── Admin/
│   │   └── Dashboard.vue       (DONE)
│   ├── Client/                 (TODO)
│   └── Owner/
│       ├── Dashboard.vue       (DONE)
│       └── Properties/
│           ├── Index.vue       (DONE)
│           ├── Create.vue      (DONE)
│           ├── Photos.vue      (DONE)
│           └── Availabilities.vue (DONE)
└── i18n/
    ├── fr.json                 (DONE - comprehensive)
    ├── ar.json                 (DONE - comprehensive)
    └── en.json                 (DONE - comprehensive)
```

## Design Decisions
- Primary color: Orange (orange-500)
- Font: Figtree (default from Breeze)
- RTL support via `:dir="isRtl ? 'rtl' : 'ltr'"`
- Mobile-first responsive design
- Sidebar navigation for admin/owner dashboards
- Card-based layouts with hover effects
- Multi-step form for property creation (5 steps)

## Files Created (Total: 20)
1. resources/js/Layouts/AppLayout.vue
2. resources/js/Layouts/AdminLayout.vue
3. resources/js/Layouts/OwnerLayout.vue
4. resources/js/Components/Layout/AppHeader.vue
5. resources/js/Components/Layout/AppFooter.vue
6. resources/js/Components/Layout/LanguageSelector.vue
7. resources/js/Components/Property/PropertyCard.vue
8. resources/js/Components/Property/ImageUploader.vue
9. resources/js/Components/Property/AmenitySelector.vue
10. resources/js/Components/Property/AvailabilityCalendar.vue
11. resources/js/Components/Forms/WilayaCommuneSelect.vue
12. resources/js/Components/Forms/PriceInput.vue
13. resources/js/Pages/Home.vue
14. resources/js/Pages/Search.vue
15. resources/js/Pages/Owner/Dashboard.vue
16. resources/js/Pages/Admin/Dashboard.vue
17. resources/js/Pages/Owner/Properties/Index.vue
18. resources/js/Pages/Owner/Properties/Create.vue
19. resources/js/Pages/Owner/Properties/Photos.vue
20. resources/js/Pages/Owner/Properties/Availabilities.vue

## API Endpoints Used
- GET `/api/wilayas/{id}/communes` - Load communes for wilaya
- GET `/api/currencies/exchange-rates` - Get EUR/USD rates
- POST `/owner/properties` - Create property
- PUT `/owner/properties/{id}` - Update property
- DELETE `/owner/properties/{id}` - Delete property
- POST `/owner/properties/{id}/images` - Upload image
- DELETE `/owner/properties/{id}/images/{imageId}` - Delete image
- PUT `/owner/properties/{id}/images/{imageId}/primary` - Set primary image
- GET/POST `/api/owner/properties/{id}/availabilities` - Manage availability

## Remaining Tasks
1. **Property Detail Page** - `Pages/Property/Show.vue`
2. **Owner Edit Page** - `Pages/Owner/Properties/Edit.vue` (reuse Create)
3. **Booking Flow** - Date picker + payment pages
4. **Client Dashboard** - Reservations/favorites
5. **Payment Pages** - Baridi/Stripe components
6. **Real Estate Promotions** - List/Detail pages
7. **i18n Composable** - Create useI18n() hook for components

## Notes for Backend Integration
- Need routes for all owner property management
- Forms submit multipart/form-data for images
- Inertia's useForm handles validation errors display
- Components accept props from Laravel controllers
