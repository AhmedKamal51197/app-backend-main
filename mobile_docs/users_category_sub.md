# User Category and SubCategory Endpoint

This document outlines the usage and structure for the new `category` and `sub_category` API endpoints for users. It is designed to assist the mobile development team in implementing the feature.

## 1. Edit User Category & SubCategory

**Endpoint:** `POST /api/v1/user/edit/category`  
**Authentication:** Required (Bearer Token)  
**Description:** Updates the main `category_id` and `sub_category_id` for the authenticated user.

### Request Body

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `category_id` | Integer | Yes | The ID of the selected Category |
| `sub_category_id` | Integer | Yes | The ID of the selected SubCategory |

**Example Request:**
```json
{
  "category_id": 2,
  "sub_category_id": 5
}
```

### Response

The response returns the updated User object via `UserResource`, which now includes `category` and `sub_category` objects.

**Example Response:**
```json
{
  "status": true,
  "message": "Operation Successful",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000",
    "name": "John Doe",
    "email": "john@example.com",
    "category": {
      "id": 2,
      "name_ar": "تصميم",
      "name_en": "Design"
    },
    "sub_category": {
      "id": 5,
      "name_ar": "تصميم ويب",
      "name_en": "Web Design"
    }
  }
}
```

---

## 2. Updated User Profile & Provider Profile

The `category` and `sub_category` objects have been added to the following endpoints:

1. **User Profile (Self):** `GET /api/v1/user/profile`
2. **Provider Profile (Viewed by Seeker):** `GET /api/v1/providers/{user_uuid}`

Both endpoints will now reliably return the `category` and `sub_category` objects at the root of the user data object if they are set.

**Example Snippet:**
```json
{
  "data": {
    "id": "uuid-here",
    "name": "Provider Name",
    "category": {
      "id": 2,
      "name_ar": "تصميم",
      "name_en": "Design"
    },
    "sub_category": {
      "id": 5,
      "name_ar": "تصميم ويب",
      "name_en": "Web Design"
    }
  }
}
```

## Notes for Mobile Developers
- Ensure you pass valid `category_id` and `sub_category_id` that exist in the database, otherwise, the API will return a 422 Validation Error.
- The user can change their category at any time using the new `POST /api/v1/user/edit/category` endpoint.
