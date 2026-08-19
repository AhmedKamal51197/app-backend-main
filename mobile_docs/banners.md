# Banners Documentation for Mobile

This documentation provides details for the Banner endpoints available for the Moawen mobile application.

## Base URL
`{{host}}/api/v1/banners`

---

## 1. List All Banners
Retrieve a list of active banners.

### Endpoint
`GET /`

### Query Parameters
| Parameter | Type | Description | Required | Default |
| :--- | :--- | :--- | :--- | :--- |
| `limit` | integer | Number of records to return per page. | No | System default (usually 10-15) |
| `type` | string | Filter banners by type. Valid values: `main`, `category`. | No | - |

### Response Structure
```json
{
    "status": "success",
    "data": [
        {
            "id": "uuid-string",
            "type": "banner-type",
            "key": "unique-key",
            "title": "Banner Title",
            "description": "Banner Description",
            "image": {
                "id": "attachment-uuid",
                "path": "https://example.com/storage/banners/image.png",
                "type": "image",
                "mime_type": "image/png"
            },
            "is_active": true,
            "created_at": "2024-05-07 02:00:00",
            "updated_at": "2024-05-07 02:00:00"
        }
    ]
}
```

---

## 2. Get Banner by ID
Retrieve details for a specific banner using its UUID.

### Endpoint
`GET /{banner_uuid}`

### Response Structure
Same as the object structure in the List response.

---

## 3. Get Banner by Key
Retrieve details for a specific banner using its unique key.

### Endpoint
`GET /key/{key}`

### Response Structure
Same as the object structure in the List response.

---

## Data Models

### Banner Object
| Field | Type | Description |
| :--- | :--- | :--- |
| `id` | string (UUID) | Unique identifier for the banner. |
| `type` | string | The category/type of the banner. |
| `key` | string | A unique human-readable key (useful for hardcoding specific banner positions in the app). |
| `title` | string | The localized title of the banner. |
| `description` | string | The localized description of the banner. |
| `image` | object | Attachment object containing the image URL and metadata. |
| `is_active` | boolean | Status of the banner. |
| `created_at` | datetime | Creation timestamp (YYYY-MM-DD HH:MM:SS). |
| `updated_at` | datetime | Last update timestamp (YYYY-MM-DD HH:MM:SS). |
