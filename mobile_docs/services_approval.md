# Services — Category Fix & Approval Status

This document describes two changes to the services API that affect the mobile app.

---

## 1. Category Fix — Edit Service

### What changed
`category_id` and `sub_category_id` are now:
- **Accepted** in the edit service request body (they were previously ignored).
- **Returned** at the root level of every service response, so form dropdowns can pre-populate correctly.

### New fields in Service response
| Field            | Type   | Description                                                   |
| :--------------- | :----- | :------------------------------------------------------------ |
| `category_id`    | string | UUID of the service's category (matches the `id` in `/api/v1/categories`). |
| `sub_category_id`| string | UUID of the service's sub-category.                          |

These are in addition to the existing nested `category` and `sub_category` objects.

### Edit Service — updated request body
`POST /api/v1/services/{service_uuid}/edit`

| Field              | Type   | Required | Description                            |
| :----------------- | :----- | :------- | :------------------------------------- |
| `title`            | string | No       | Service title.                         |
| `description`      | string | No       | Service description.                   |
| `client_guidelines`| string | No       | Guidelines for the client.             |
| `category_id`      | string | No       | UUID of the new category.              |
| `sub_category_id`  | string | No       | UUID of the new sub-category.          |

> Sending `category_id` or `sub_category_id` will also reset `is_approved` to `false`, requiring admin re-approval.

---

## 2. Approval Workflow — What the Mobile App Should Know

### New field in Service response
| Field         | Type    | Description                                                    |
| :------------ | :------ | :------------------------------------------------------------- |
| `is_approved` | boolean | `true` = visible to seekers. `false` = pending admin approval. |

### Behavior
- **After creating a service**: `is_approved` will be `false`. The service will not appear in the public listing for seekers until an admin approves it from the dashboard.
- **After editing a service**: `is_approved` resets to `false`, even if it was previously approved.
- **Freelancer's own service list**: The freelancer can still see their own services (including pending ones) via the authenticated my-services endpoint.
- **Public seeker listing**: Only services with `is_approved = true` are returned.

### Recommended UX
- After a freelancer creates or edits a service, display a message such as:
  > "Your service is under review and will be visible to seekers once approved by an admin."
- In the freelancer's service list, show a "Pending Approval" badge when `is_approved === false`.

---

## Service Response Example

```json
{
  "success": true,
  "data": {
    "id": "<uuid>",
    "title": "Logo Design",
    "category_id": "<category-uuid>",
    "sub_category_id": "<sub-category-uuid>",
    "category": { "id": "<uuid>", "title": "Design", ... },
    "sub_category": { "id": "<uuid>", "title": "Branding", ... },
    "is_approved": false,
    ...
  }
}
```
