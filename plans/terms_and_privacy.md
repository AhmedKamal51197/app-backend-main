# Implementation Plan: Terms of Use & Privacy Policy

## Goal
Make the "Terms of Use" and "Privacy Policy" pages available for the mobile app to display on the account registration page. The data must be accessible via an API without requiring authentication (No Auth), while keeping the content editable from the Backend Admin Panel.

## Current Project Status
After reviewing the codebase, the backend is almost completely prepared for this feature:
1. **Database & Models**: A `Page` model and a `pages` database table already exist with the required columns (`key`, `content`, `is_active`).
2. **Admin Panel Control**: An Admin-specific controller (`Admin\PageController`) is already implemented with functions to view and edit pages (`index`, `show`, `update`).
3. **Mobile API (No Auth)**: There is an existing public route `GET /api/v1/pages/{page:key}` which maps to `Api\Page\PageController@show`. This endpoint correctly returns the page content by its key and **does not require authentication**.
4. **Initial Data**: `PageSeeder.php` currently contains seed data for `privacy_policy`, but it is missing the data for `terms_of_use`.

## Proposed Execution Plan

### 1. Add "Terms of Use" to the Seeder
- Update the `database/seeders/PageSeeder.php` file.
- Add a new array for `terms_of_use` containing default content, right next to the existing `privacy_policy`.
- Run `php artisan db:seed --class=PageSeeder` to inject the new page into the database so it becomes editable in the admin panel.

### 2. Clean Up Legacy API Routes (Recommended)
- There is an old, restrictive route in `routes/Api/User/privacy_policy_routes.php` that uses the `auth:api` middleware. 
- It is highly recommended to remove or deprecate this old route, as the mobile app will rely entirely on the new public dynamic `pages` endpoint.

### 3. Mobile App Integration (Frontend/Mobile)
- To fetch the Privacy Policy, the mobile app should call: `GET /api/v1/pages/privacy_policy`
- To fetch the Terms of Use, the mobile app should call: `GET /api/v1/pages/terms_of_use`
- Neither request requires a `Bearer Token`. The backend will directly return the page data so the mobile team can display the `content` on the registration page.

### 4. Admin Panel Integration (Backend/Frontend)
- The admin dashboard will use the existing backend endpoints:
  - To list all pages: `GET /api/v1/admin/pages`
  - To update page content: `PUT /api/v1/admin/pages/{uuid}` with the new `content` field.
- No new backend logic is required for the admin panel; the frontend just needs to consume these existing routes.

---
**Summary**: The backend infrastructure is approximately 95% complete. The only actual code change required is adding the "Terms of Use" default data to the `PageSeeder.php` so it exists in the database and can be fetched by the mobile application.
