# FCM Documentation for Mobile

This documentation provides details for the FCM (Firebase Cloud Messaging) integration in the Moawen mobile application.

## Overview
The mobile app should send the `fcm_token` during registration and login. This token is stored on the server and used to send push notifications to the user.

---

## 1. User Registration
The `fcm_token` can be optionally sent during registration.

### Endpoint
`POST /api/auth/register`

### Request Body (Addition)
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `fcm_token` | string | The Firebase Cloud Messaging token for the device. | No |

---

## 2. User Login
The `fcm_token` should be sent during login to ensure the server has the most recent token for the device.

### Endpoint
`POST /api/auth/login`

### Request Body (Addition)
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `fcm_token` | string | The Firebase Cloud Messaging token for the device. | No |

---

## 3. User Resource Object
The `fcm_token` is now included in the user object returned by various endpoints (profile, login, etc.).

### Response Structure (Fragment)
```json
{
    "id": "uuid-string",
    "name": "User Name",
    "email": "user@example.com",
    "fcm_token": "current-stored-fcm-token",
    ...
}
```

---

## 4. Test Notification Endpoint
A dedicated endpoint is available to test notification delivery to a specific token.

### Endpoint
`POST /api/v1/test/send-notification`

### Request Body
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `token` | string | The FCM token to send the notification to. | Yes |
| `title` | string | The title of the notification. | Yes |
| `body` | string | The body text of the notification. | Yes |

### Example Request
```json
{
    "token": "eXample_Token_123...",
    "title": "Hello from Moawen",
    "body": "This is a test notification."
}
```

### Response
```json
{
    "status": "success",
    "message": "Notification sent successfully"
}
```

---

## Implementation Notes for Mobile
- **Token Updates**: It is recommended to send the FCM token on every login to keep it synchronized.
- **Notification Click**: The server sends `click_action: FLUTTER_NOTIFICATION_CLICK` in the data payload for better integration with Flutter background handling.
