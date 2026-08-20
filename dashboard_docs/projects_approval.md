# Projects — Approval Workflow

This document covers the **Project Approval Workflow** — newly created projects are hidden from users until an admin approves them. Admins can approve or explicitly reject a project from the dashboard.

## Base URL
`{{host}}/admin/v1/projects`

## Authentication
All endpoints require an admin/supervisor web session (`administrator` middleware) plus the required permission.

---

## New Endpoints

### 1. Approve / Revoke Approval

Toggle the approval status of a project. Calling this on an already-approved project revokes approval (sets `is_approved = false`).

```
POST /{project_uuid}/approve
```

**Required Permission:** `Approve Projects`

**URL Parameters**
| Parameter     | Type   | Description                          |
| :------------ | :----- | :----------------------------------- |
| `project_uuid` | string | UUID of the project to approve. |

**Request Body:** None.

**Success Response `200`**
```json
{
  "error": false,
  "message": "",
  "data": {
    "id": "<uuid>",
    "title": "...",
    "status": "pending",
    "is_approved": true,
    ...
  }
}
```

---

### 2. Reject (explicit)

Set the approval status back to `false` explicitly. Use this to reject a project that was previously approved.

```
POST /{project_uuid}/reject
```

**Required Permission:** `Reject Projects`

**URL Parameters**
| Parameter     | Type   | Description                         |
| :------------ | :----- | :---------------------------------- |
| `project_uuid` | string | UUID of the project to reject. |

**Request Body:** None.

**Success Response `200`**
```json
{
  "error": false,
  "message": "Project rejected successfully",
  "data": {
    "id": "<uuid>",
    "title": "...",
    "status": "pending",
    "is_approved": false,
    ...
  }
}
```

---

## Updated Field: `is_approved`

The `is_approved` field is now included in **all** project responses from the admin API (list and detail).

| Value   | Meaning                                            |
| :------ | :------------------------------------------------- |
| `false` | Pending / rejected — not visible to users.         |
| `true`  | Approved — visible to users publicly.              |

### When `is_approved` becomes `false`
- A user **creates** a project → starts as `false`.
- An admin **rejects** a project → set to `false`.
- An admin **toggles** the approve endpoint on an approved project → back to `false`.

---

## Existing Endpoints (unchanged)

| Method | URI | Permission | Description |
| :--- | :--- | :--- | :--- |
| GET | `/` | `Index Projects` | List all projects (including unapproved), with `status` filter. |
| GET | `/{project_uuid}` | `Details Projects` | Project details. |
| POST | `/{project_uuid}/approve-cancellation` | `Approve Project Cancellation` | Approve a cancellation request. |
| POST | `/{project_uuid}/reject-cancellation` | `Reject Project Cancellation` | Reject a cancellation request. |

List query parameters (optional):
```
?status=pending&search=word&limit=15&page=1&order_by=latest
```

---

## Data Model Changes

### Project Object (additions)
| Field         | Type    | Description                                        |
| :------------ | :------ | :------------------------------------------------- |
| `is_approved` | boolean | Whether the project has been approved by an admin. |

---

## Migration & Seeder Note

Run the following after deploying:

```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

- `migrate` adds the `is_approved` boolean column (default `false`) to the `projects` table.
- The seeder registers the two new permissions: `Approve Projects` and `Reject Projects`.

> **Important**: All existing projects will have `is_approved = false` after migration. To keep previously published projects visible, run this one-time command:
>
> ```bash
> php artisan tinker --execute="App\Models\Project::query()->where('status', '!=', 'draft')->update(['is_approved' => true]);"
> ```

> **Note**: After seeding, the root role gets both new permissions automatically. Grant `Approve Projects` / `Reject Projects` to supervisor roles manually from the Roles screen.