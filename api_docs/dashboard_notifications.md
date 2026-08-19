# Dashboard Notifications API Documentation

This documentation provides details for the Dashboard Notification endpoints used by administrators.

## Base URL
`{{host}}/admin/v1/dashboard-notifications`

---

## 1. List Notifications
Retrieve a paginated list of dashboard notifications.

### Endpoint
`GET /`

### Query Parameters
| Parameter | Type | Description |
| :--- | :--- | :--- |
| `type` | string | Filter by type (`normal`, `dispute`). |
| `resolved` | boolean | Filter by resolution status. |
| `is_seen` | boolean | Filter by seen status. |
| `limit` | integer | Number of items per page (default 15). |

---

## 2. Store Notification
Create a new notification manually.

### Endpoint
`POST /add`

### Request Body
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `title` | string | Notification title. | Yes |
| `details` | string | Detailed description. | No |
| `type` | string | `normal` or `dispute`. | Yes |
| `resolved` | boolean | Initial resolution status. | No |
| `is_seen` | boolean | Initial seen status. | No |

---

## 3. Get Details
Retrieve details of a specific notification.

### Endpoint
`GET /{uuid}`

---

## 4. Update Notification
Update notification details.

### Endpoint
`POST /{uuid}/edit`

---

## 5. Delete Notification
Delete a notification (soft delete).

### Endpoint
`DELETE /{uuid}/delete`

---

## 6. Mark as Seen
Quick action to mark a notification as seen.

### Endpoint
`POST /{uuid}/mark-as-seen`

---

## 7. Mark as Resolved
Quick action to mark a notification as resolved.

### Endpoint
`POST /{uuid}/mark-as-resolved`

---

## Data Models

### Dashboard Notification Object
| Field | Type | Description |
| :--- | :--- | :--- |
| `id` | string (UUID) | Unique identifier. |
| `title` | string | Notification title. |
| `details` | string | Notification details. |
| `type` | string | `normal` or `dispute`. |
| `resolved` | boolean | Whether the issue is resolved. |
| `is_seen` | boolean | Whether the admin has seen it. |
| `created_at` | datetime | YYYY-MM-DD HH:MM:SS |
| `updated_at` | datetime | YYYY-MM-DD HH:MM:SS |
