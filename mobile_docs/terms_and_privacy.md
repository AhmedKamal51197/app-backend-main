# Terms of Use & Privacy Policy — Mobile Integration Guide

## Overview

The Terms of Use and Privacy Policy pages are stored in the database and managed via the Admin Panel. The mobile app fetches the latest content dynamically through a public API — **no authentication token is required**.

These pages are intended to be displayed on the account registration screen.

---

## Endpoints

### Get Privacy Policy

```
GET /api/v1/pages/privacy_policy
```

### Get Terms of Use

```
GET /api/v1/pages/terms_of_use
```

**Authentication**: None — these endpoints are fully public.

---

## Response Format

Both endpoints return the same structure:

```json
{
  "success": true,
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

| Field        | Type    | Description                              |
|--------------|---------|------------------------------------------|
| `id`         | string  | UUID of the page record                  |
| `key`        | string  | Page identifier (`privacy_policy` / `terms_of_use`) |
| `content`    | string  | Full page content (Arabic text)          |
| `is_active`  | boolean | Whether the page is active               |
| `created_at` | string  | Creation timestamp (`Y-m-d H:i:s`)      |
| `updated_at` | string  | Last update timestamp (`Y-m-d H:i:s`)   |

### Error Response (page not active or not found)

```json
{
  "success": false,
  "message": "Page not found"
}
```

HTTP status: `404`

---

## Usage Example

Fetch and display the Terms of Use on the registration screen:

```dart
// Flutter example
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/pages/terms_of_use'),
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body)['data'];
  final content = data['content'] as String;
  // Display content in a scrollable Text widget
}
```

---

## Notes

- Content is in **Arabic** and may contain multi-line text — use a scrollable view.
- The content is editable from the Admin Panel at any time; always fetch it fresh rather than hardcoding it.
- The old authenticated endpoint `GET /api/v1/privacy-policy` (required `Bearer Token`) has been removed. Use the public endpoints above.
- To display both pages, make two parallel requests: one for `privacy_policy` and one for `terms_of_use`.
