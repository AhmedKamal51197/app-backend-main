# Supervisor Management API

Documentation for managing supervisors (Admin users with specific roles).

## 1. Add Supervisor
Add a new supervisor user with a specific role and password.

- **URL**: `/api/v1/roles/supervisor-users/add`
- **Method**: `POST`
- **Auth**: Required (Admin)
- **Permissions**: `Add Roles` (or specific supervisor permission if configured)

### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | Yes | Full name of the supervisor (min 3 chars) |
| `email` | string | Yes | Unique email address |
| `mobile` | string | Yes | Unique mobile number |
| `role_id` | string (UUID) | Yes | The UUID of the role to assign |
| `password` | string | Yes | Account password (min 8 chars) |

### Response (200 OK)
```json
{
    "error": false,
    "message": "Supervisor created successfully",
    "data": {
        "id": "user-uuid",
        "name": "John Doe",
        "email": "john@example.com",
        "mobile": "123456789",
        "roles": [
            {
                "id": "role-uuid",
                "name": "Supervisor"
            }
        ]
    }
}
```

---

## 2. Edit Supervisor
Update supervisor profile information and role.

- **URL**: `/api/v1/roles/supervisor-users/{user-uuid}/edit`
- **Method**: `POST`
- **Auth**: Required (Admin)

### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | Yes | Full name |
| `email` | string | Yes | Unique email |
| `mobile` | string | Yes | Unique mobile number |
| `role_id` | string (UUID) | Yes | The UUID of the role |

### Response (200 OK)
```json
{
    "error": false,
    "message": "Supervisor updated successfully",
    "data": { ... }
}
```

---

## 3. Update Supervisor Password
Change the password for a supervisor account.

- **URL**: `/api/v1/roles/supervisor-users/{user-uuid}/update-password`
- **Method**: `POST`
- **Auth**: Required (Admin)

### Request Body
| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `password` | string | Yes | New password (min 8 chars) |
| `password_confirmation` | string | Yes | Must match `password` |

### Response (200 OK)
```json
{
    "error": false,
    "message": "Supervisor password updated successfully",
    "data": []
}
```
