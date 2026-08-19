# Withdrawal Settings API Documentation

This documentation provides details for managing withdrawal-related system settings.

## Base URL
`{{host}}/admin/v1/settings`

---

## 1. Update Withdrawal Settings
Update the global limits and minimum amounts for payment requests.

### Endpoint
`POST /withdrawal-settings`

### Request Body
| Field | Type | Description | Required |
| :--- | :--- | :--- | :--- |
| `daily_payment_requests_amount` | numeric | Maximum allowed withdrawal amount per day. | Yes |
| `monthly_payment_requests_amount` | numeric | Maximum allowed withdrawal amount per month. | Yes |
| `minimum_payment_request_amount` | numeric | Minimum allowed amount for a single withdrawal request. | Yes |

### Response
```json
{
    "success": true,
    "message": "Withdrawal settings updated successfully",
    "data": []
}
```

---

## 2. Get All Settings
Retrieve all system settings (includes the new withdrawal settings).

### Endpoint
`GET /`

### Response
Look for items with `setting_name`:
- `daily_payment_requests_amount`
- `monthly_payment_requests_amount`
- `minimum_payment_request_amount`
