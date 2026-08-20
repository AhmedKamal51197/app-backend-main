# Projects — Approval Workflow (Mobile)

This document explains how projects behave on the mobile app after the **Project Approval Workflow** was added.

## Overview

A project created by a user starts as **unapproved** (`is_approved = false`). It is **not visible** in the public browse list until an admin approves it from the dashboard.

Users can still see **their own** projects in every state (draft, pending, approved, rejected) via the "My Projects" endpoint.

## Base URL
`{{host}}/api/v1/projects`

## Authentication
All endpoints require a Bearer token (`Authorization: Bearer {{access_token}}`).

---

## 1. Create Project

```
POST /add
Content-Type: application/json
```

**Request Body**
```json
{
    "title": "Logo design",
    "description": "I need a logo for a startup",
    "min_price": 200,
    "max_price": 500,
    "time": 7,
    "category_id": "category-uuid",
    "sub_category_id": "sub-category-uuid",
    "attachments": []
}
```

**Result:** Project created with `status: draft` and `is_approved: false`.

---

## 2. Publish Project

```
POST /{project_uuid}/publish
```

Changes `status` from `draft` → `pending`. The project is still **unapproved** — it needs admin approval before being publicly visible.

---

## 3. My Projects (see your own in every state)

```
GET /my-projects
```

Returns **all** of the current user's projects regardless of approval/status. Each item includes:

| Field         | Type    | Meaning                                   |
| :------------ | :------ | :---------------------------------------- |
| `status`      | string  | `draft`, `pending`, `in_progress`, `completed`, ... |
| `is_approved` | boolean | `true` approved ✅ / `false` under review or rejected ⏳ |

**Example response item**
```json
{
  "id": "uuid",
  "title": "Logo design",
  "status": "pending",
  "is_approved": false,
  "min_price": "200.00",
  "max_price": "500.00",
  ...
}
```

Use `is_approved` to show a "Under Review" / "Approved" / "Rejected" badge to the user.

---

## 4. Browse Projects (public)

```
GET /?limit=15&page=1
```

Returns **only approved** projects (`is_approved: true`, and not `cancelled`/`draft`). New or rejected projects do **not** appear here.

---

## 5. Other Project Actions

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/{project_uuid}` | Project details. |
| POST | `/{project_uuid}/cancel` | Cancel project. |
| POST | `/{project_uuid}/complete` | Complete project. |
| POST | `/{project_uuid}/proposals/{proposal_uuid}/accept` | Accept a proposal. |

---

## Flow Summary

```
1. User      → POST /projects/add                (is_approved = false)
2. User      → POST /projects/{id}/publish       (status: draft → pending)
3. Admin     → GET /admin/v1/projects            (sees it with is_approved: false)
4. Admin     → POST /admin/v1/projects/{id}/approve   → approved ✅
             or POST /admin/v1/projects/{id}/reject   → rejected ⏳
5. Users     → GET /projects                     (approved ones appear)
6. Owner     → GET /projects/my-projects         (sees own projects + is_approved)
```