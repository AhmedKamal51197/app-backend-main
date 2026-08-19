# Services — Approval Workflow & Category Fix

This document covers two changes to the services feature:
1. **Service Approval Workflow** — newly created or edited services are hidden from seekers until an admin approves them.
2. **Category Bug Fix** — admins can now see `is_approved` on every service, and the API correctly saves/returns category and sub-category when a freelancer edits a service.

## Base URL
`{{host}}/admin/v1/services`

## Authentication
All endpoints require an admin Bearer token and the `administrator` middleware.

---

## New Endpoint: Approve / Revoke Approval

Toggle the approval status of a service. Calling this endpoint on an already-approved service will revoke approval (set `is_approved = false`).

### Endpoint
`POST /{service_uuid}/approve`

### Required Permission
`Approve Services`

### URL Parameters
| Parameter       | Type   | Description                     |
| :-------------- | :----- | :------------------------------ |
| `service_uuid`  | string | UUID of the service to approve. |

### Request Body
None.

### Success Response `200`
```json
{
  "success": true,
  "data": {
    "id": "<uuid>",
    "title": "...",
    "is_approved": true,
    "is_enabled": true,
    "hidden": false,
    ...
  }
}
```

---

## Updated Field: `is_approved`

The `is_approved` field is now included in **all** service responses from the admin API (list and detail).

| Value   | Meaning                                              |
| :------ | :--------------------------------------------------- |
| `false` | Pending — service is not visible to seekers.         |
| `true`  | Approved — service is visible to seekers publicly.   |

### When `is_approved` becomes `false`
- A freelancer **creates** a new service → starts as `false`.
- A freelancer **edits** an existing service → resets to `false`, requiring re-approval.

---

## Existing Endpoints (unchanged)

### List Services
`GET /` — requires `Index Services` permission.

The list now includes `is_approved` on each item, so the dashboard table can display a "Pending Approval" badge.

### Get Service Details
`GET /{service_uuid}` — requires `Details Services` permission.

### Toggle Visibility (Hidden)
`POST /{service_uuid}/toggle-hidden` — requires `Toggle Service Visibility` permission.

### Delete Service
`DELETE /{service_uuid}/delete` — requires `Delete Services` permission.

---

## Data Model Changes

### Service Object (additions)
| Field         | Type    | Description                                             |
| :------------ | :------ | :------------------------------------------------------ |
| `is_approved` | boolean | Whether the service has been approved by an admin.      |

---

## Migration Note
Run the following after deploying:

```bash
php artisan migrate
```

This adds the `is_approved` boolean column (default `false`) to the `services` table.

> **Important**: All existing services will have `is_approved = false` after migration. Run the following one-time command to approve all pre-existing services so they remain visible:
>
> ```bash
> php artisan tinker --execute="App\Models\Service::query()->update(['is_approved' => true]);"
> ```
