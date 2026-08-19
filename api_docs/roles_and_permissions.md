# Roles and Permissions Documentation

This document describes the role management system and the available access levels.

## Role Access Levels

When creating or updating a role, you must specify an `access_level`. This determines the default permission preset for the role.

### Available Values
| Value | Description |
| :--- | :--- |
| `full_access` | Grants all available system permissions. |
| `edit_only` | Grants permissions related to viewing and editing, but may restrict deletion or sensitive actions. |
| `custom` | No default permissions; permissions must be assigned manually. |

---

## API Endpoints

### 1. List Roles
`GET /admin/v1/roles`

### 2. Add Role
`POST /admin/v1/roles/add`

#### Request Body
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `name` | string | Role name (technical). | Yes |
| `name_ar` | string | Role name in Arabic. | No |
| `allowed_user` | boolean | Whether this role can be assigned to front-end users. | Yes |
| `type` | string | `technical`, `administrative`, `view_only`. | Yes |
| `access_level` | string | `full_access`, `edit_only`, `custom`. | Yes |
| `description` | string | Role description. | No |
| `permissions` | array | Array of permission names (e.g., `["Index Users", "Edit Users"]`). | No |

---

## Technical Implementation Details

### Enum
- **Class**: `App\Enums\RoleAccessLevelEnum`
- **Cases**:
  - `FULL_ACCESS` = `'full_access'`
  - `EDIT_ONLY` = `'edit_only'`
  - `CUSTOM` = `'custom'`

### Validation
The `AddRoleRequest` and `EditRoleRequest` strictly validate the `access_level` against the Enum values.
