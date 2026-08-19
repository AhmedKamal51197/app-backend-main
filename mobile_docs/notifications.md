# Notifications

This document explains the push notification endpoints available for the mobile application.

## Endpoints

### 1. Send Custom Notification
Send a custom push notification to the currently authenticated user's device. This is primarily used for testing push notifications directly on the device.

**URL:** `POST /api/v1/user/send-notification`  
**Authentication:** Required (Bearer Token)  

#### Request Body
The endpoint expects a JSON payload containing the notification content.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | **Yes** | The title of the push notification (max 255 characters). |
| `body` | string | **Yes** | The main body/message of the push notification. |

**Example Request:**
```json
{
  "title": "Welcome Back!",
  "body": "This is a test notification from the API."
}
```

#### Response
**200 OK**
```json
{
  "success": true,
  "message": "Notification sent successfully",
  "data": []
}
```

#### Error Responses
- **401 Unauthorized:** If the user's bearer token is missing or invalid.
- **422 Unprocessable Entity:** If the `title` or `body` is missing.

---

## Push Notification Payload Structure

When the device receives a push notification from the backend, the payload structure provided by Firebase Cloud Messaging (FCM) will look like this:

```json
{
  "notification": {
    "title": "Welcome Back!",
    "body": "This is a test notification from the API."
  },
  "data": {
    "type": "custom_notification"
  }
}
```

The `data.type` field can be used by the mobile app to handle navigation or custom UI updates. For this specific endpoint, the type is always `"custom_notification"`.
