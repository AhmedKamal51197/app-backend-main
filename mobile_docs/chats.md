# Chats Endpoints

This document outlines the API endpoints for managing and opening chats.

## 1. Open or Create a Chat

**Endpoint:** `POST /api/v1/chats/open`  
**Authentication:** Required (Bearer Token)  
**Description:** This endpoint is used to either create a new chat or open an existing one. The request parameters depend on the **type** of chat you are trying to initiate.

### Request Body

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `type` | String | Yes | The type of the chat. Must be one of: `user`, `team`, `service`, `part_time_job`, `project` |
| `title` | String | No | An optional title for the chat. |
| `user_uuid` | String | **Required if type is `user`** | The UUID of the user you want to chat with. |
| `team_uuid` | String | **Required if type is `team`** | The UUID of the team. |
| `service_uuid` | String | **Required if type is `service`** | The UUID of the service. |
| `part_time_job_uuid` | String | **Required if type is `part_time_job`**| The UUID of the part-time job. |
| `project_uuid` | String | **Required if type is `project`** | The UUID of the project. |

### Example Requests

**A. Opening a direct chat with another user:**
```json
{
  "type": "user",
  "user_uuid": "00000000-0000-0000-0000-000000000000"
}
```

**B. Opening a chat for a specific service:**
```json
{
  "type": "service",
  "service_uuid": "00000000-0000-0000-0000-000000000000"
}
```

### Response

The response returns the Chat object (including the participants and latest messages).

**Example Response:**
```json
{
  "status": true,
  "message": "Chat opened successfully",
  "data": {
    "id": "chat-uuid",
    "type": "service",
    "title": "Service Discussion",
    "chattable_type": "App\\Models\\Service",
    "chattable_id": 1,
    "participants": [
      // array of users in the chat
    ],
    "messages": [
      // array of chat messages
    ]
  }
}
```

## Important Notes for Mobile
1. **Validation Error (422):** If you pass the wrong UUID key for the selected type (e.g., passing `user_uuid` when `type` is `service`), the API will return a validation error stating the required UUID is missing.
2. **Access Control (403):** 
   - You cannot start a `user` chat with yourself.
   - For `team` chats, you must be a member of the team whose `team_uuid` you are passing.
