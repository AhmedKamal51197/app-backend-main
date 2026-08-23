# Freelance Service Publishing Flow — Investigation Report

## 1. Overview

The Freelance App allows providers to create services (one-time or part-time) that seekers can purchase. Services go through a creation flow and become visible to the public based on a combination of boolean flags. An `is_approved` approval mechanism **already partially exists** but has critical gaps that prevent it from functioning as a complete admin review workflow.

**Key finding:** The `is_approved` column, the `store()` default, the `edit()` reset, the public listing filter, and the admin `approve()` toggle are all already implemented. However, there are **bypass paths** (Home, Search) that do not filter by `is_approved`, the `Approve Services` permission is not seeded via the standard seeder, and there is no `reject()` method or route.

---

## 2. Route Map

### 2.1 User API Routes (`routes/Api/User/services_routes.php`)

| Purpose | HTTP Method | Route | Controller | Method |
|---------|-------------|-------|------------|--------|
| Browse all services | GET | `api/v1/services/` | `Api\Service\ServiceController` | `index` |
| My services | GET | `api/v1/services/my-services` | `Api\Service\ServiceController` | `indexUserServices` |
| Interest-based services | GET | `api/v1/services/interests` | `Api\Service\ServiceController` | `indexInterests` |
| Create service | POST | `api/v1/services/add` | `Api\Service\ServiceController` | `store` |
| Show service | GET | `api/v1/services/{service}/` | `Api\Service\ServiceController` | `show` |
| Edit service | POST | `api/v1/services/{service}/edit` | `Api\Service\ServiceController` | `edit` |
| Delete service | DELETE | `api/v1/services/{service}/delete` | `Api\Service\ServiceController` | `delete` |
| Add attachment | POST | `api/v1/services/{service}/attachments/add` | `Api\Service\ServiceController` | `addAttachment` |
| Delete attachment | DELETE | `api/v1/services/{service}/attachments/{attachment}/delete` | `Api\Service\ServiceController` | `deleteAttachment` |
| Edit package | POST | `api/v1/services/{service}/packages/{package}/edit` | `Api\Service\ServiceController` | `editPackage` |
| Add skills | POST | `api/v1/services/{service}/skills/add` | `Api\Service\ServiceController` | `addSkills` |
| Remove skills | POST | `api/v1/services/{service}/skills/delete` | `Api\Service\ServiceController` | `removeSkills` |

**Middleware:** `auth:api` (Passport token)

### 2.2 Admin API Routes (`routes/Api/Admin/services_routes.php`)

| Purpose | HTTP Method | Route | Controller | Method | Permission |
|---------|-------------|-------|------------|--------|------------|
| List all services | GET | `admin/v1/services/` | `Admin\Service\ServiceController` | `index` | `Index Services` |
| Show service | GET | `admin/v1/services/{service}/` | `Admin\Service\ServiceController` | `show` | `Details Services` |
| Toggle hidden | POST | `admin/v1/services/{service}/toggle-hidden` | `Admin\Service\ServiceController` | `toggleHidden` | `Toggle Service Visibility` |
| Toggle approval | POST | `admin/v1/services/{service}/approve` | `Admin\Service\ServiceController` | `approve` | `Approve Services` |
| Delete service | DELETE | `admin/v1/services/{service}/delete` | `Admin\Service\ServiceController` | `delete` | `Delete Services` |

**Middleware:** `api`, `auth:api`, `administrator`, `active` + per-route `permission:` middleware

### 2.3 Other Service-Related Routes

| Purpose | HTTP Method | Route | Controller | Method |
|---------|-------------|-------|------------|--------|
| Home one-time services | GET | `api/v1/home/one-time-services` | `Api\Home\HomeController` | `oneTimeServices` |
| Home part-time services | GET | `api/v1/home/part-time-services` | `Api\Home\HomeController` | `partTimeServices` |
| Search by sub-category | POST | `api/v1/search/sub-category` | `Api\Service\ServiceSearchController` | `__invoke` |
| General search | POST | `api/v1/search/` | `Api\Search\SearchController` | `search` |
| Service checkout | POST | `api/v1/checkouts/service` | `Api\Checkout\ServiceCheckoutController` | `checkout` |
| Favorites list | GET | `api/v1/favorites/` | `Api\Favorite\FavoriteServiceController` | `index` |
| Add to favorites | POST | `api/v1/favorites/{service}/add` | `Api\Favorite\FavoriteServiceController` | `store` |
| Remove favorite | DELETE | `api/v1/favorites/{service}/delete` | `Api\Favorite\FavoriteServiceController` | `delete` |
| Admin user services | GET | `admin/v1/users/{user}/services` | `Admin\Users\UserDetailsController` | `services` |

---

## 3. Service Creation Flow

### Request Flow

```
POST /api/v1/services/add
  → Api\Service\ServiceController::store()                    [line 118]
    → StoreServiceRequest::authorize()                        [auth()->check()]
    → StoreServiceRequest::rules()                            [validates title, description, etc.]
    → ServiceService::store($user, $validated)                [line 136]
      → Category::where('uuid', $data['category_id'])->firstOrFail()
      → SubCategory::where('uuid', $data['sub_category_id'])->firstOrFail()
      → DB::beginTransaction()
      → Service::create([
            'title'             => $data['title'],
            'description'       => $data['description'],
            'client_guidelines' => $data['client_guidelines'],
            'category_id'       => $category->id,
            'sub_category_id'   => $subCategory->id,
            'type'              => $data['type'],
            'hidden'            => false,
            'custom_offer'      => false,
            'is_approved'       => false,       ← HARDCODED FALSE
            'user_id'           => $user->id,
        ])
      → StoreAttachmentAction::store($service, $data['attachments'])
      → ServiceService::storeServiceSkills($service, $data['skill_ids'])
      → ServiceService::storeServicePackages($service, $data['packages'])
      → DB::commit()
      → Notify user (OneTimeServiceAddedNotification or PartTimeServiceAddedNotification)
      → Return loaded service
```

### Key Files

| Layer | File | Line(s) |
|-------|------|---------|
| Route | `routes/Api/User/services_routes.php` | 10 |
| Controller | `app/Http/Controllers/Api/Service/ServiceController.php` | 118-126 |
| Form Request | `app/Http/Requests/Api/Service/StoreServiceRequest.php` | 1-147 |
| Service Class | `app/Services/Service/ServiceService.php` | 136-178 |
| Action | `app/Actions/Attachments/StoreAttachmentAction.php` | — |
| Model | `app/Models/Service.php` | 1-207 |

### Validation Rules (StoreServiceRequest)

- `title` — required, string, max:255
- `description` — required, string
- `client_guidelines` — required, string
- `category_id` — required, string, exists:categories,uuid
- `sub_category_id` — required, string, exists:sub_categories,uuid
- `attachments` — required, array, min:1, max:5
- `skill_ids` — sometimes, array, min:1
- `type` — required, enum: one_time | part_time
- `packages` — required, array; size:2 for part_time, min:1/max:3 for one_time
- `packages.*.title` — nullable, string
- `packages.*.price` — required, numeric, min:1
- `packages.*.days` — required, numeric, min:1
- `packages.*.revisions` — required when unlimited_revisions=false
- `packages.*.unlimited_revisions` — required, boolean
- `packages.*.feature_ids` — required, array, min:1

---

## 4. Publishing Flow

### Current Visibility Rules

A service becomes visible/public when **ALL** of the following are true:

| Field | Required Value | Set By |
|-------|---------------|--------|
| `is_enabled` | `true` | Admin `toggleHidden()` (actually sets `hidden`, not `is_enabled`) |
| `hidden` | `false` | Default on create; admin `toggleHidden()` |
| `is_approved` | `true` | Admin `approve()` toggle |
| `custom_offer` | `false` | Default on create |

**Applied in:** `ServiceService::index()` (line 50-53) and `ServiceService::indexByUserInterests()` (line 117-120):

```php
$query->where('is_enabled', true)
      ->where('hidden', false)
      ->where('custom_offer', false)
      ->where('is_approved', true);
```

### Visibility Filtering by Endpoint

| Endpoint | Filters `is_approved`? | Filters `is_enabled`? | Filters `hidden`? | Filters `custom_offer`? |
|----------|----------------------|---------------------|-------------------|----------------------|
| `GET /api/v1/services/` (browse) | YES | YES | YES | YES |
| `GET /api/v1/services/interests` | YES | YES | YES | YES |
| `GET /api/v1/services/my-services` | NO | NO | NO | YES (custom_offer only) |
| `GET /api/v1/services/{service}` (show) | **NO** | **NO** | **NO** | **NO** |
| `GET /api/v1/home/one-time-services` | **NO** | **NO** | YES | **NO** |
| `GET /api/v1/home/part-time-services` | **NO** | **NO** | YES | YES |
| `POST /api/v1/search/sub-category` | **NO** | **NO** | YES | **NO** |
| `POST /api/v1/search/` | **NO** | **NO** | YES | **NO** |
| `GET /admin/v1/services/` (admin) | **NO** | **NO** | **NO** | **NO** |

**CRITICAL:** The Home, Search, and Show endpoints do NOT filter by `is_approved` or `is_enabled`. This means unapproved/disabled services can still be seen through these paths.

---

## 5. Status Lifecycle

There is no single `status` enum. Service state is determined by four independent boolean fields:

| Field | Column Type | Default | Cast | Meaning |
|-------|-------------|---------|------|---------|
| `is_enabled` | boolean | `true` | **NOT cast** | Admin enable/disable toggle |
| `hidden` | boolean | `false` | `boolean` | Hidden from public browse |
| `is_approved` | boolean | `false` | `boolean` | Admin approval gate |
| `custom_offer` | boolean | `false` | `boolean` | Custom offer (excluded from browse) |

### State Transitions

| Event | `is_enabled` | `hidden` | `is_approved` | `custom_offer` |
|-------|-------------|----------|---------------|----------------|
| New service created | `true` (default) | `false` (hardcoded) | `false` (hardcoded) | `false` (hardcoded) |
| Service edited | unchanged | unchanged | **reset to `false`** | unchanged |
| Admin toggles hidden | unchanged | **toggled** | unchanged | unchanged |
| Admin toggles approval | unchanged | unchanged | **toggled** | unchanged |
| Service deleted | — | — | — | — (soft delete) |

### Effective States (Combination)

| Effective State | is_enabled | hidden | is_approved | custom_offer | Visible in Browse? |
|----------------|------------|--------|-------------|--------------|-------------------|
| Pending review | true | false | **false** | false | **NO** |
| Approved & visible | true | false | **true** | false | YES |
| Admin disabled | **false** | false | true | false | NO |
| Admin hidden | true | **true** | true | false | NO |
| Custom offer | true | false | true | **true** | NO |
| Fully hidden | true | true | false | false | NO |

---

## 6. Database

### `services` Table

| Column | Type | Default | Migration | Notes |
|--------|------|---------|-----------|-------|
| `id` | bigint (PK) | auto | original | Auto-increment |
| `uuid` | uuid | auto-generated | original | HasUuid trait |
| `title` | string | — | original | |
| `description` | longText | — | original | |
| `client_guidelines` | text | nullable | `2026_02_12_140124` | |
| `category_id` | FK → categories | — | original | constrained |
| `sub_category_id` | FK → sub_categories | — | original | constrained |
| `user_id` | FK → users | — | original | constrained |
| `type` | enum('one_time','part_time') | `'one_time'` | original | |
| `is_enabled` | boolean | `true` | `2025_06_26_175701` | NOT cast in model |
| `hidden` | boolean | `false` | `2025_07_27_115318` | Cast to boolean |
| `is_approved` | boolean | `false` | `2026_06_02_000000` | Cast to boolean |
| `custom_offer` | boolean | `false` | `2025_07_27_122802` | Cast to boolean |
| `deleted_at` | softDeletes | nullable | original | |
| `created_at` | timestamp | auto | original | |
| `updated_at` | timestamp | auto | original | |

### Related Tables

- `service_packages` — pricing packages (FK → services, columns: uuid, title, price, days, revisions, unlimited_revisions)
- `service_tags` — skill tags (FK → services + skills)
- `favorite_services` — user favorites (FK → users + services)
- `attachments` — polymorphic media (attachable_type = Service)

### Key Relationships (Service Model)

| Method | Type | Related | Notes |
|--------|------|---------|-------|
| `user()` | BelongsTo | User | Service provider/owner |
| `category()` | BelongsTo | Category | Top-level category |
| `subCategory()` | BelongsTo | SubCategory | Always eager-loaded (`$with`) |
| `attachments()` | MorphMany | Attachment | Polymorphic |
| `skills()` | HasMany | ServiceTag | Skill tags |
| `packages()` | HasMany | ServicePackage | Pricing packages |
| `favorites()` | HasMany | FavoriteService | User favorites |
| `rates()` | HasManyThrough | Rate (via Order) | Ratings through orders |
| `orders()` | HasManyThrough | Order (via ServicePackage) | All orders |
| `chats()` | MorphMany | Chat | Polymorphic |

---

## 7. Authorization

### Admin Authorization Stack

All admin service routes inherit (from `RouteServiceProvider` line 43-45):

1. `api` — global API middleware group
2. `auth:api` — Passport token authentication
3. `administrator` — checks user has a role with `allowed_user = false`
4. `active` — checks user account is active

Plus per-route `permission:` middleware (Spatie):

| Route | Permission | Defined in Seeder? |
|-------|------------|-------------------|
| `GET /admin/v1/services/` | `Index Services` | YES |
| `GET /admin/v1/services/{service}/` | `Details Services` | YES |
| `POST /admin/v1/services/{service}/toggle-hidden` | `Toggle Service Visibility` | YES |
| `POST /admin/v1/services/{service}/approve` | `Approve Services` | **MISSING from `RolesAndPermissionsSeeder`** (exists in `SyncSystemPermissions` command) |
| `DELETE /admin/v1/services/{service}/delete` | `Delete Services` | YES |

### User Authorization

All user service routes only require `auth:api`. **No ownership checks exist.**

- No `ServicePolicy` exists
- No `Gate::define` for services
- `StoreServiceRequest::authorize()` returns `auth()->check()` only
- `EditServiceRequest::authorize()` returns `auth()->check()` only
- The `Api\Service\ServiceController` does not verify `$service->user_id === $request->user()->id`

### Permission Seeder Gap

The `RolesAndPermissionsSeeder` defines these service permissions:
- `Index Services`
- `Details Services`
- `Toggle Service Visibility`
- `Delete Services`

**Missing from seeder:**
- `Approve Services` (referenced in route middleware but not seeded)
- `Reject Services` (does not exist anywhere)

The `SyncSystemPermissions` artisan command (`php artisan permissions:sync`) DOES define `Approve Services`, but this must be run manually.

---

## 8. Admin Review — Existing Implementation

### What Already Exists

The `is_approved` pattern for services is **already partially implemented**:

| Component | Status | Location |
|-----------|--------|----------|
| `is_approved` column | EXISTS | `database/migrations/2026_06_02_000000_add_is_approved_to_services_table.php` |
| Model cast | EXISTS | `app/Models/Service.php:47` — `'is_approved' => 'boolean'` |
| Default `false` on create | EXISTS | `app/Services/Service/ServiceService.php:152` — `'is_approved' => false` |
| Reset on edit | EXISTS | `app/Services/Service/ServiceService.php:197` — `'is_approved' => false` |
| Public listing filter | EXISTS | `app/Services/Service/ServiceService.php:53` — `->where('is_approved', true)` |
| Interests filter | EXISTS | `app/Services/Service/ServiceService.php:120` — `->where('is_approved', true)` |
| Admin `approve()` toggle | EXISTS | `app/Http/Controllers/Admin/Service/ServiceController.php:97-101` |
| Admin `/approve` route | EXISTS | `routes/Api/Admin/services_routes.php:12` |
| Admin Resource field | EXISTS | `app/Http/Resources/Admin/Service/ServiceResource.php:36` |

### What Is Missing

| Component | Status | Notes |
|-----------|--------|-------|
| Admin `reject()` method | MISSING | No explicit reject; only toggle exists |
| `/reject` route | MISSING | No route for explicit rejection |
| `Approve Services` permission | MISSING from seeder | Must run `php artisan permissions:sync` |
| `Reject Services` permission | MISSING entirely | Does not exist in seeder or sync command |
| `is_approved` filter on Home | MISSING | `HomeService` does not filter by `is_approved` |
| `is_approved` filter on Search | MISSING | `SearchService` does not filter by `is_approved` |
| `is_approved` filter on Show | MISSING | `ServiceController@show` does not check `is_approved` |
| `ServicePolicy` | MISSING | No ownership or approval policy |

### Comparison with Project Approval Pattern

| Aspect | Project | Service |
|--------|---------|---------|
| Migration | `2026_08_20_000000_add_is_approved_to_projects_table.php` | `2026_06_02_000000_add_is_approved_to_services_table.php` |
| Model cast | Yes | Yes |
| Default false on create | Yes | Yes |
| Reset on edit | N/A (no edit) | Yes |
| Public listing filter | Yes | Yes (browse + interests only) |
| Admin `approve()` toggle | Yes | Yes |
| Admin `reject()` method | YES | **MISSING** |
| `/approve` route | Yes | Yes |
| `/reject` route | YES | **MISSING** |
| Permissions seeded | Yes | **PARTIAL** (`Approve Services` missing from seeder) |
| Home filter | N/A | **MISSING** |
| Search filter | N/A | **MISSING** |

---

## 9. All Publishing Entry Points

### Entry Points That CAN Make a Service Visible

| # | Entry Point | Sets `is_approved`? | Notes |
|---|------------|---------------------|-------|
| 1 | `POST /api/v1/services/add` (store) | Sets to `false` | Service starts unapproved |
| 2 | `POST /admin/v1/services/{service}/approve` | **Toggles** | Only way to set `is_approved = true` |

### Entry Points That BYPASS `is_approved` Filter

| # | Endpoint | Shows unapproved services? | Evidence |
|---|----------|--------------------------|----------|
| 1 | `GET /api/v1/services/{service}/` (show) | **YES** | No `is_approved` filter in controller |
| 2 | `GET /api/v1/home/one-time-services` | **YES** | `HomeService::oneTimeServices()` only filters `hidden` |
| 3 | `GET /api/v1/home/part-time-services` | **YES** | `HomeService::partTimeServices()` only filters `hidden` + `custom_offer` |
| 4 | `POST /api/v1/search/sub-category` | **YES** | `SearchService::searchServices()` only filters `hidden` |
| 5 | `POST /api/v1/search/` | **YES** | `SearchService::buildServiceQuery()` only filters `hidden` |

### Entry Points That DO NOT Modify `is_approved`

| # | Action | Does it affect `is_approved`? |
|---|--------|------------------------------|
| 1 | `POST /api/v1/services/{service}/edit` | Resets to `false` |
| 2 | `POST /admin/v1/services/{service}/toggle-hidden` | No (only `hidden`) |
| 3 | `DELETE /admin/v1/services/{service}/delete` | N/A (soft delete) |
| 4 | `POST /api/v1/services/{service}/packages/{package}/edit` | No |
| 5 | `POST /api/v1/services/{service}/skills/add` | No |
| 6 | `POST /api/v1/services/{service}/skills/delete` | No |

### Commands/Scheduled Tasks

| Command | Modifies services? | Affects `is_approved`? |
|---------|-------------------|----------------------|
| `services:clean-part-time-packages` | Yes (deletes extra packages) | No |
| `permissions:sync` | No (permissions only) | No |
| `db:seed --class=RolesAndPermissionsSeeder` | No (permissions only) | No |

**No scheduled tasks modify services.** No jobs modify services. No observers modify services.

---

## 10. Recommended Implementation Approach

### Current State Summary

The `is_approved` mechanism is **already 70% implemented**. The column exists, the creation defaults to `false`, the edit resets to `false`, the public browse filters by `true`, and the admin toggle exists. The main gaps are:

1. **Bypass paths** — Home, Search, and Show endpoints don't filter by `is_approved`
2. **Missing reject flow** — No reject method or route
3. **Missing permissions** — `Approve Services` not in seeder; `Reject Services` doesn't exist
4. **No ServicePolicy** — No ownership or approval authorization

### Recommended Changes

#### A. Close Bypass Paths (Critical)

**File: `app/Services/Home/HomeService.php`**

Add `->where('is_approved', true)` to both `oneTimeServices()` (line 28-39) and `partTimeServices()` (line 49-63).

**File: `app/Services/Search/SearchService.php`**

Add `->where('is_approved', true)` to `searchServices()` (lines 31-35, 43-47) and `buildServiceQuery()` (line 128-131).

**File: `app/Http/Controllers/Api/Service/ServiceController.php`**

In `show()` (line 102-107), add a check: if `$service->is_approved === false`, return 404 or a "service not available" message.

#### B. Add Reject Flow

**File: `app/Http/Controllers/Admin/Service/ServiceController.php`**

Add a `reject()` method (following the Project pattern):

```php
public function reject(Service $service): JsonResponse
{
    $service->update(['is_approved' => false]);
    return $this->jsonSuccess(ServiceResource::make($service));
}
```

**File: `routes/Api/Admin/services_routes.php`**

Add route:

```php
Route::post('/{service}/reject', [ServiceController::class, 'reject'])
    ->middleware('permission:Reject Services');
```

#### C. Seed Missing Permissions

**File: `database/seeders/RolesAndPermissionsSeeder.php`**

Add to the services permissions block:

```php
'Approve Services' => 'اعتماد الخدمات',
'Reject Services' => 'رفض الخدمات',
```

Also run `php artisan permissions:sync` to ensure `Approve Services` exists.

#### D. Optional: Add ServicePolicy

Create `app/Policies/ServicePolicy.php` with ownership checks. Register in `AuthServiceProvider`. Use in controller methods to prevent users from editing/deleting other users' services.

### Implementation Priority

| Priority | Change | Risk | Effort |
|----------|--------|------|--------|
| 1 | Close Home/Search bypass paths | Low | Small |
| 2 | Seed `Approve Services` + `Reject Services` permissions | Low | Tiny |
| 3 | Add `reject()` method + route | Low | Small |
| 4 | Close Show endpoint bypass | Low | Small |
| 5 | Add ServicePolicy (ownership checks) | Medium | Medium |

### What Does NOT Need to Change

- **Migration** — `is_approved` column already exists
- **Model** — cast already defined
- **Store flow** — already sets `is_approved = false`
- **Edit flow** — already resets `is_approved = false`
- **Browse filter** — already filters `is_approved = true`
- **Interests filter** — already filters `is_approved = true`
- **Admin approve toggle** — already works
- **Admin Resource** — already exposes `is_approved`

---

## Appendix: File Reference

| File | Purpose |
|------|---------|
| `app/Models/Service.php` | Service model with relationships and casts |
| `app/Services/Service/ServiceService.php` | Core business logic (store, edit, index, delete) |
| `app/Services/Service/AdminServiceService.php` | Admin listing with analytics |
| `app/Services/Home/HomeService.php` | Home page service listings (MISSING `is_approved` filter) |
| `app/Services/Search/SearchService.php` | Search service listings (MISSING `is_approved` filter) |
| `app/Http/Controllers/Api/Service/ServiceController.php` | User-facing API controller |
| `app/Http/Controllers/Admin/Service/ServiceController.php` | Admin API controller |
| `app/Http/Requests/Api/Service/StoreServiceRequest.php` | Create service validation |
| `app/Http/Requests/Api/Service/EditServiceRequest.php` | Edit service validation |
| `app/Http/Resources/Api/Service/ServiceResource.php` | API response transform |
| `app/Http/Resources/Admin/Service/ServiceResource.php` | Admin response transform (includes `is_approved`) |
| `routes/Api/User/services_routes.php` | User service routes |
| `routes/Api/Admin/services_routes.php` | Admin service routes |
| `database/migrations/2026_06_02_000000_add_is_approved_to_services_table.php` | `is_approved` column migration |
| `database/seeders/RolesAndPermissionsSeeder.php` | Permission seeder (missing `Approve Services`) |
| `app/Console/Commands/SyncSystemPermissions.php` | Permission sync (has `Approve Services`) |
