# Implementation Plan: Fix Category Bug & Add Service Approval Workflow

This plan addresses two requests:
1. Fixing the issue where the main category is not saved/updated properly when adding or editing a service.
2. Implementing an approval workflow so that newly created services are hidden from seekers until an admin approves them from the dashboard.

## ⚠️ Open Questions for User
> [!IMPORTANT]
> 1. **Editing Approved Services**: If a freelancer edits a service that is *already approved*, should it revert to `is_approved = false` and require admin approval again? (The plan assumes **YES** for security, but please confirm).
> 2. **Default Status for Existing Services**: Should all *currently existing* services in the database be automatically marked as approved, or should they all be hidden until you manually approve them? (The plan assumes we will run a quick script to mark all existing services as approved so they don't suddenly disappear).

---

## Proposed Changes

### 1. Fix Category Saving Bug
Currently, the category is correctly saved in the database upon creation, but the frontend might be failing to display it because the API response doesn't expose the raw `category_id`, or because editing a service drops the category update. 

#### [MODIFY] `app/Http/Requests/Api/Service/EditServiceRequest.php`
- Add validation rules for `category_id` and `sub_category_id` so freelancers can actually change the category when editing a service.

#### [MODIFY] `app/Services/Service/ServiceService.php`
- Update the `edit()` method to look up the Category and SubCategory by UUID and save their IDs if they are provided in the update request.

#### [MODIFY] `app/Http/Resources/Api/Service/ServiceResource.php`
- Expose `category_id` and `sub_category_id` (the UUIDs) at the root level of the JSON response. This ensures frontend form bindings work perfectly and don't reset the dropdown after a service is added or edited.

---

### 2. Service Approval Workflow

#### [NEW] `database/migrations/xxxx_xx_xx_xxxxxx_add_is_approved_to_services_table.php`
- Create a new migration to add a boolean column `is_approved` to the `services` table, with a default value of `false`.

#### [MODIFY] `app/Models/Service.php`
- Add `is_approved` to the `$casts` array as a `boolean`.

#### [MODIFY] `app/Services/Service/ServiceService.php`
- Modify the public `index()` method. When seekers are browsing services (`$isUserServices == false`), add a condition to only return services where `is_approved == true`.
- Modify the `store()` method to ensure new services are created with `is_approved = false`.
- Modify the `edit()` method to reset `is_approved = false` when a freelancer modifies their service (pending your answer to the open question).

#### [MODIFY] `routes/api.php` or `routes/admin.php`
- Add a new Admin route: `POST /api/v1/admin/services/{service}/approve` to allow admins to approve or reject a service.

#### [MODIFY] `app/Http/Controllers/Admin/Service/ServiceController.php`
- Add an `approve()` method that toggles the `is_approved` status of a service and returns the updated status.

#### [MODIFY] `app/Http/Resources/Admin/Service/ServiceResource.php`
- Add `is_approved` to the admin resource so the dashboard table can display whether a service is "Pending Approval" or "Approved".

---

## Verification Plan
1. **Run Migrations**: Ensure the new `is_approved` column is added successfully.
2. **Category Test**: Create and edit a service via the API, verifying that `category_id` and `sub_category_id` are saved, updated, and returned correctly in the JSON response.
3. **Approval Test**: 
   - Create a new service. Verify it is **not** visible in the public services list.
   - Call the Admin Approve endpoint.
   - Verify the service **is** now visible in the public services list.
