# Pages API Documentation (Terms of Use & Privacy Policy)

This documentation covers the admin endpoints for managing the Terms of Use and Privacy Policy pages.

## Base URL
`{{host}}/admin/v1/pages`

## Authentication
All endpoints require an admin Bearer token and the `administrator` middleware.

---

## 1. List All Pages
Retrieve a list of all pages.

### Endpoint
`GET /`

### Required Permission
`Index Pages`

### Query Parameters
| Parameter | Type    | Description                                   |
| :-------- | :------ | :-------------------------------------------- |
| `limit`   | integer | Number of items per page (default: system setting). |

### Success Response `200`
```json
{
  "success": true,
  "data": [
    {
      "id": "<uuid>",
      "key": "privacy_policy",
      "content": "...",
      "is_active": true,
      "created_at": "2025-08-25 00:00:00",
      "updated_at": "2025-08-25 00:00:00"
    },
    {
      "id": "<uuid>",
      "key": "terms_of_use",
      "content": "...",
      "is_active": true,
      "created_at": "2025-08-25 00:00:00",
      "updated_at": "2025-08-25 00:00:00"
    }
  ]
}
```

---

## 2. Get Page Details
Retrieve a single page by its key.

### Endpoint
`GET /{key}`

### Required Permission
`Details Pages`

### URL Parameters
| Parameter | Type   | Description                                          |
| :-------- | :----- | :--------------------------------------------------- |
| `key`     | string | Page key — `privacy_policy` or `terms_of_use`. |

### Success Response `200`
```json
{
  "success": true,
  "data": {
    "id": "<uuid>",
    "key": "terms_of_use",
    "content": "...",
    "is_active": true,
    "created_at": "2025-08-25 00:00:00",
    "updated_at": "2025-08-25 00:00:00"
  }
}
```

---

## 3. Update Page Content
Update the content or active status of a page.

### Endpoint
`POST /{key}/edit`

### Required Permission
`Edit Pages`

### URL Parameters
| Parameter | Type   | Description                                          |
| :-------- | :----- | :--------------------------------------------------- |
| `key`     | string | Page key — `privacy_policy` or `terms_of_use`. |

### Request Body
| Field       | Type    | Required | Description                          |
| :---------- | :------ | :------- | :----------------------------------- |
| `content`   | string  | Yes      | Full page content.                   |
| `is_active` | boolean | No       | Whether the page is publicly visible. |

### Success Response `200`
```json
{
  "success": true,
  "message": "Page updated successfully",
  "data": {
    "id": "<uuid>",
    "key": "privacy_policy",
    "content": "...",
    "is_active": true,
    "created_at": "2025-08-25 00:00:00",
    "updated_at": "2025-08-25 00:00:00"
  }
}
```

### Validation Error Response `422`
```json
{
  "success": false,
  "message": "The content field is required."
}
```

---

## Data Model

### Page Object
| Field        | Type    | Description                                              |
| :----------- | :------ | :------------------------------------------------------- |
| `id`         | string  | UUID of the page record.                                 |
| `key`        | string  | Page identifier (`privacy_policy` / `terms_of_use`).    |
| `content`    | string  | Full page content (Arabic text, multi-line).             |
| `is_active`  | boolean | When `false`, the public mobile endpoint returns `404`.  |
| `created_at` | string  | Creation timestamp (`Y-m-d H:i:s`).                     |
| `updated_at` | string  | Last update timestamp (`Y-m-d H:i:s`).                  |

---

## Available Page Keys
| Key              | Description       |
| :--------------- | :---------------- |
| `privacy_policy` | Privacy Policy page displayed on the registration screen. |
| `terms_of_use`   | Terms of Use page displayed on the registration screen.   |

---

## Notes
- Setting `is_active` to `false` hides the page from the mobile app (returns `404`). Use with caution.
- Content changes are reflected immediately in the mobile app on the next fetch — no cache or deployment needed.
- The mobile app fetches these pages via the public endpoints `GET /api/v1/pages/{key}` — see `mobile_docs/terms_and_privacy.md`.
