# Moawen Backend — Routes & How to Call Them

Complete reference for all API and Admin routes in the backend, with authentication and calling conventions.

---

## Base URLs

| Environment | URL |
| :--- | :--- |
| Mobile API (v1) | `{{host}}/api/v1` |
| Mobile Auth | `{{host}}/api/auth` |
| Admin Dashboard API (v1) | `{{host}}/admin/v1` |
| Dashboard (web) | `{{host}}/dashboard` |

---

## How to Call (Conventions)

### Headers

Every request to `api/*` should include:

| Header | Value | Description |
| :--- | :--- | :--- |
| `Accept` | `application/json` | Required for API JSON responses |
| `Accept-Language` | `ar` or `en` | Language of the response (default: `ar`) |
| `Authorization` | `Bearer {access_token}` | **Required** for authenticated endpoints |
| `Content-Type` | `application/json` | For POST/PUT/PATCH with JSON body |

### Authentication Flow (Mobile API)

1. Call `POST /api/auth/register` or `POST /api/auth/login`.
2. You receive an `access_token` (Passport OAuth2 Bearer token).
3. Send it in the `Authorization: Bearer {access_token}` header for all protected endpoints.
4. `email_verified`, `mobile_verified`, `kyc_verified` flags come back in the auth response.

### Authentication Flow (Admin Dashboard)

Admin routes use **web session** auth (not bearer tokens):

1. `POST /login` (Laravel Fortify) with `email` + `password`.
2. Routes require the `administrator` middleware → the user must have a role with `allowed_user = false` (admin/supervisor).
3. Many routes also require **permissions** (`permission:...` middleware). Missing permission → `403`.

### Auth Protected vs Public

- Public (no token): banners, categories, sub-categories, skills, certificate-providers, country, faqs, features, pages, auth, test/send-notification.
- Everything else under `api/v1/*` requires `auth:api`.

---

## 1. Mobile Auth Routes (`/api/auth`)

| Method | URI | Auth | Description |
| :--- | :--- | :--- | :--- |
| POST | `/api/auth/register` | No | Register a new user |
| POST | `/api/auth/login` | No | Login & get token |
| POST | `/api/auth/logout` | Yes | Logout (invalidates token) |
| POST | `/api/auth/password/forget` | No | Send password reset link/email |
| POST | `/api/auth/password/reset` | No | Reset password with token |
| POST | `/api/auth/reset-password/send/otp` | No | Send OTP for password reset |
| POST | `/api/auth/reset-password/verify/otp` | No | Verify OTP |
| POST | `/api/auth/reset-password/reset` | No | Reset password using OTP |

### Example: Register

```bash
curl -X POST "{{host}}/api/auth/register" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmed",
    "email": "ahmed@example.com",
    "password": "secret123",
    "password_confirmation": "secret123",
    "country_id": "country-uuid",
    "role": "seeker",
    "fcm_token": "optional-fcm-token"
  }'
```

**Response:** `{ "status": "success", "data": { "access_token": "...", "email_verified": false, "mobile_verified": false, "kyc_verified": false, "expires_at": "...", "user_data": {...}, "permissions": [] } }`

### Example: Login

```bash
curl -X POST "{{host}}/api/auth/login" \
  -H "Accept: application/json" -H "Accept-Language: ar" \
  -H "Content-Type: application/json" \
  -d '{"email": "ahmed@example.com", "password": "secret123", "fcm_token": "optional"}'
```

---

## 2. Mobile API Routes (`/api/v1`)

### Public (No Auth)

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/banners` | List banners (`?limit=`, `?type=`) |
| GET | `/api/v1/banners/{banner}` | Get banner by UUID |
| GET | `/api/v1/banners/key/{key}` | Get banner by key |
| GET | `/api/v1/categories` | List categories |
| GET | `/api/v1/categories/{category}` | Get category |
| GET | `/api/v1/categories/{category}/sub-categories` | Sub-categories of a category |
| GET | `/api/v1/categories/{category}/skills` | Skills of a category |
| GET | `/api/v1/sub-categories/{subCategory}` | Get sub-category |
| GET | `/api/v1/skills/{skill}` | Get skill |
| GET | `/api/v1/certificate-providers` | List certificate providers |
| GET | `/api/v1/certificate-providers/{certificateProvider}` | Get certificate provider |
| GET | `/api/v1/country` | List countries |
| GET | `/api/v1/faqs` | List FAQs |
| GET | `/api/v1/faqs/{faq}` | Get FAQ |
| GET | `/api/v1/features` | List features |
| GET | `/api/v1/features/{feature}` | Get feature |
| GET | `/api/v1/pages/{key}` | Get static page by key |
| POST | `/api/v1/test/send-notification` | Test FCM push (body: `token`, `title`, `body`) |

### Authenticated — User & Profile

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/user/profile` | Get current user profile |
| POST | `/api/v1/user/edit/profile` | Edit profile |
| POST | `/api/v1/user/edit/avatar` | Change profile picture |
| POST | `/api/v1/user/change/password` | Change password |
| POST | `/api/v1/user/change-status` | Change user status |
| GET | `/api/v1/user/home` | User home feed |
| GET | `/api/v1/user/dashboard` | User analytics/dashboard |
| GET | `/api/v1/user/notifications` | User notifications list |
| POST | `/api/v1/user/send-notification` | Send a notification (FCM) |
| GET | `/api/v1/home/one-time-services` | Home — one-time services |
| GET | `/api/v1/home/part-time-services` | Home — part-time services |
| GET | `/api/v1/home/users` | Home — users |

### Authenticated — User Categories / Sub-Categories / Skills / Certificates

| Method | URI | Description |
| :--- | :--- | :--- |
| POST | `/api/v1/user/store/category` | Attach category to user |
| POST | `/api/v1/user/edit/category` | Edit user categories |
| POST | `/api/v1/user/store/sub-categories` | Attach sub-categories |
| POST | `/api/v1/user/delete/sub-categories` | Remove sub-categories |
| POST | `/api/v1/user/store/skills` | Attach skills |
| POST | `/api/v1/user/delete/skills` | Remove skills |
| POST | `/api/v1/user/store/certificate` | Add certificate |
| POST | `/api/v1/user/edit/certificate/{certificate}` | Edit certificate |
| POST | `/api/v1/user/delete/certificate/{certificate}` | Delete certificate |

### Authenticated — Services

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/services` | List services |
| POST | `/api/v1/services/add` | Create service |
| GET | `/api/v1/services/my-services` | My services |
| GET | `/api/v1/services/{service}` | Get service |
| POST | `/api/v1/services/{service}/edit` | Edit service |
| DELETE | `/api/v1/services/{service}/delete` | Delete service |
| POST | `/api/v1/services/{service}/attachments/add` | Add attachment |
| DELETE | `/api/v1/services/{service}/attachments/{attachment}/delete` | Remove attachment |
| POST | `/api/v1/services/{service}/skills/add` | Add skills to service |
| POST | `/api/v1/services/{service}/skills/delete` | Remove skills |
| POST | `/api/v1/services/{service}/packages/{package}/edit` | Edit service package |

### Authenticated — Favorites

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/favorites` | List favorite services |
| POST | `/api/v1/favorites/{service}/add` | Add service to favorites |
| DELETE | `/api/v1/favorites/{service}/delete` | Remove service from favorites |

### Authenticated — Portfolios

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/portfolios` | List portfolios |
| GET | `/api/v1/portfolios/info` | Portfolio info/form data |
| GET | `/api/v1/portfolios/favorites` | Favorite portfolios |
| GET | `/api/v1/portfolios/users/{user}` | Portfolios of a user |
| POST | `/api/v1/portfolios/add` | Create portfolio |
| GET | `/api/v1/portfolios/{portfolio}` | Get portfolio |
| POST | `/api/v1/portfolios/{portfolio}/edit` | Edit portfolio |
| DELETE | `/api/v1/portfolios/{portfolio}/delete` | Delete portfolio |
| POST | `/api/v1/portfolios/{portfolio}/attachments/add` | Add attachment |
| DELETE | `/api/v1/portfolios/{portfolio}/attachments/{attachment}/delete` | Remove attachment |
| POST | `/api/v1/portfolios/{portfolio}/skills/add` | Add skills |
| POST | `/api/v1/portfolios/{portfolio}/skills/delete` | Remove skills |
| POST | `/api/v1/portfolios/{portfolio}/toggle-favorite` | Toggle favorite |
| POST | `/api/v1/portfolios/{portfolio}/toggle-hidden` | Toggle hidden |

### Authenticated — Jobs

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/jobs` | List jobs |
| POST | `/api/v1/jobs/add` | Create job |
| GET | `/api/v1/jobs/{job}` | Get job |
| POST | `/api/v1/jobs/{job}/edit` | Edit job |
| DELETE | `/api/v1/jobs/{job}/delete` | Delete job |
| POST | `/api/v1/jobs/{job}/attachments/add` | Add attachment |
| DELETE | `/api/v1/jobs/{job}/attachments/{attachment}/delete` | Remove attachment |
| POST | `/api/v1/jobs/{job}/skills/add` | Add skills |
| POST | `/api/v1/jobs/{job}/skills/delete` | Remove skills |

### Authenticated — Educations & Experiences

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/educations` | List educations |
| GET | `/api/v1/educations/info` | Education form data |
| POST | `/api/v1/educations/add` | Add education |
| GET | `/api/v1/educations/{education}` | Get education |
| POST | `/api/v1/educations/{education}/edit` | Edit education |
| DELETE | `/api/v1/educations/{education}/delete` | Delete education |
| GET | `/api/v1/experiences` | List experiences |
| GET | `/api/v1/experiences/info` | Experience form data |
| POST | `/api/v1/experiences/add` | Add experience |
| GET | `/api/v1/experiences/{experience}` | Get experience |
| POST | `/api/v1/experiences/{experience}/edit` | Edit experience |
| DELETE | `/api/v1/experiences/{experience}/delete` | Delete experience |

### Authenticated — KYC

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/kycs` | List my KYCs |
| POST | `/api/v1/kycs/add` | Submit KYC |
| GET | `/api/v1/kycs/{kyc}` | Get KYC |

### Authenticated — Wallets & Payment

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/wallets` | List wallets |
| GET | `/api/v1/wallets/getbalance` | Get wallet balance |
| POST | `/api/v1/user/wallet/add` | Add money to wallet |
| GET | `/api/v1/bank-account` | Get my bank account |
| POST | `/api/v1/bank-account/store` | Store bank account |
| POST | `/api/v1/bank-account/update` | Update bank account |
| GET | `/api/v1/paypal` | Get my PayPal |
| POST | `/api/v1/paypal/store` | Store PayPal |
| GET | `/api/v1/payment-requests` | List payment requests |
| POST | `/api/v1/payment-requests/add` | Create payment request |
| GET | `/api/v1/payment-requests/{paymentRequest}` | Get payment request |

### Authenticated — Orders

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/orders` | List orders |
| GET | `/api/v1/orders/seeker` | Seeker orders |
| GET | `/api/v1/orders/one-time` | One-time orders |
| GET | `/api/v1/orders/part-time` | Part-time orders |
| GET | `/api/v1/orders/{order}` | Get order |
| GET | `/api/v1/orders/{order}/part-time` | Get part-time order |
| GET | `/api/v1/orders/{order}/history` | Order history |
| POST | `/api/v1/orders/{order}/approve` | Approve order |
| POST | `/api/v1/orders/{order}/reject` | Reject order |
| POST | `/api/v1/orders/{order}/release` | Release funds |
| POST | `/api/v1/orders/{order}/release-request` | Request release |
| POST | `/api/v1/orders/{order}/cancel-request` | Request cancellation |
| POST | `/api/v1/orders/{order}/dispute` | Dispute order |
| POST | `/api/v1/orders/{order}/rate` | Rate order |
| POST | `/api/v1/orders/{order}/request-revision` | Request revision |
| POST | `/api/v1/orders/{order}/messages/send` | Send order message |

### Authenticated — Checkout

| Method | URI | Description |
| :--- | :--- | :--- |
| POST | `/api/v1/checkouts/service` | Checkout a service |
| POST | `/api/v1/checkouts/project` | Checkout a project |
| POST | `/api/v1/checkouts/order` | Checkout an order |
| GET | `/api/v1/checkouts/payment-methods` | Payment methods (MyFatoorah) |

### Authenticated — Chats & Messages

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/chats` | List chats |
| POST | `/api/v1/chats/open` | Open a chat with a user |
| GET | `/api/v1/chats/{chat}` | Get chat messages |
| POST | `/api/v1/chats/{chat}/messages` | Send message |
| POST | `/api/v1/chats/{chat}/offer` | Send an offer in chat |

### Authenticated — Projects & Proposals

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/projects` | List projects |
| POST | `/api/v1/projects/add` | Create project |
| GET | `/api/v1/projects/my-projects` | My projects |
| GET | `/api/v1/projects/{project}` | Get project |
| POST | `/api/v1/projects/{project}/publish` | Publish project |
| POST | `/api/v1/projects/{project}/cancel` | Cancel project |
| POST | `/api/v1/projects/{project}/complete` | Complete project |
| POST | `/api/v1/projects/{project}/proposals/{proposal}/accept` | Accept proposal |
| GET | `/api/v1/proposals` | List proposals |
| POST | `/api/v1/proposals/add` | Submit proposal |
| GET | `/api/v1/proposals/{proposal}` | Get proposal |

### Authenticated — Providers & Search & Reports

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/api/v1/providers/{user}` | Provider profile |
| POST | `/api/v1/search` | General search |
| POST | `/api/v1/search/sub-category` | Search services by sub-category |
| GET | `/api/v1/reports` | List my reports |
| POST | `/api/v1/reports/add` | Create report |
| GET | `/api/v1/reports/{report}` | Get report |
| POST | `/api/v1/reports/{report}/close` | Close report |
| POST | `/api/v1/reports/{report}/response` | Reply to report |
| DELETE | `/api/v1/reports/{report}/delete` | Delete report |

### Authenticated — Email Verification

| Method | URI | Description |
| :--- | :--- | :--- |
| POST | `/api/v1/verify-email/send` | Send verification email |
| POST | `/api/v1/verify-email/verify` | Verify email |

### Example: Authenticated Call

```bash
curl -X GET "{{host}}/api/v1/favorites" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar" \
  -H "Authorization: Bearer {access_token}"
```

---

## 3. Admin Dashboard Routes (`/admin/v1`)

All require an admin/supervisor **web session** (`administrator` middleware). Some require `permission:...`.

| Method | URI | Permission | Description |
| :--- | :--- | :--- | :--- |
| GET | `/admin/v1/analysis` | — | Dashboard analysis |
| GET | `/admin/v1/users` | — | List users |
| GET | `/admin/v1/users/admins` | — | List admins |
| GET | `/admin/v1/users/providers` | — | List providers |
| GET | `/admin/v1/users/seekers` | — | List seekers |
| GET | `/admin/v1/users/analytics` | — | Users analytics |
| GET | `/admin/v1/users/{user}` | — | User details |
| POST | `/admin/v1/users/{user}/toggle-active` | — | Activate/deactivate user |
| DELETE | `/admin/v1/users/{user}/delete` | — | Delete user |
| GET | `/admin/v1/users/{user}/kyc` | — | User KYC list |
| GET | `/admin/v1/users/{user}/orders` | — | User orders |
| GET | `/admin/v1/users/{user}/services` | — | User services |
| GET | `/admin/v1/users/{user}/payment-requests` | — | User payment requests |
| GET | `/admin/v1/users/{user}/wallet` | — | User wallet |
| GET | `/admin/v1/roles` | — | List roles |
| POST | `/admin/v1/roles/add` | — | Create role |
| GET | `/admin/v1/roles/{role}` | — | Role details |
| POST | `/admin/v1/roles/{role}/edit` | — | Edit role |
| DELETE | `/admin/v1/roles/{role}/delete` | — | Delete role |
| POST | `/admin/v1/roles/{role}/toggle-status` | — | Toggle role status |
| POST | `/admin/v1/roles/{role}/permissions/{permission}/add` | — | Assign permission |
| DELETE | `/admin/v1/roles/{role}/permissions/{permission}/delete` | — | Revoke permission |
| GET | `/admin/v1/roles/supervisor-users` | — | List supervisor users |
| POST | `/admin/v1/roles/supervisor-users/add` | — | Create supervisor user |
| GET | `/admin/v1/roles/supervisor-users/{user}` | — | Supervisor user details |
| POST | `/admin/v1/roles/supervisor-users/{user}/edit` | — | Edit supervisor user |
| POST | `/admin/v1/roles/supervisor-users/{user}/update-password` | — | Update supervisor password |
| GET | `/admin/v1/permissions` | — | List permissions |
| POST | `/admin/v1/permissions/add` | — | Create permission |
| GET | `/admin/v1/permissions/{permission}` | — | Permission details |
| POST | `/admin/v1/permissions/{permission}/edit` | — | Edit permission |
| DELETE | `/admin/v1/permissions/{permission}/delete` | — | Delete permission |
| GET | `/admin/v1/categories` | — | List categories |
| POST | `/admin/v1/categories/add` | — | Create category |
| GET | `/admin/v1/categories/{category}` | — | Category details |
| POST | `/admin/v1/categories/{category}/edit` | — | Edit category |
| DELETE | `/admin/v1/categories/{category}/delete` | — | Delete category |
| GET | `/admin/v1/categories/{category}/sub-categories` | — | Sub-categories |
| POST | `/admin/v1/categories/{category}/sub-categories/add` | — | Add sub-category |
| GET | `/admin/v1/categories/{category}/skills` | — | Category skills |
| POST | `/admin/v1/categories/{category}/skills/add` | — | Add skill |
| GET | `/admin/v1/sub-categories/{subCategory}` | — | Sub-category details |
| POST | `/admin/v1/sub-categories/{subCategory}/edit` | — | Edit sub-category |
| DELETE | `/admin/v1/sub-categories/{subCategory}/delete` | — | Delete sub-category |
| GET | `/admin/v1/skills/{skill}` | — | Skill details |
| POST | `/admin/v1/skills/{skill}/edit` | — | Edit skill |
| DELETE | `/admin/v1/skills/{skill}/delete` | — | Delete skill |
| GET | `/admin/v1/countries` | — | List countries |
| POST | `/admin/v1/countries/add` | — | Add country |
| GET | `/admin/v1/countries/{country}` | — | Country details |
| DELETE | `/admin/v1/countries/{country}/delete` | — | Delete country |
| GET | `/admin/v1/certificate-providers` | — | List providers |
| POST | `/admin/v1/certificate-providers/add` | — | Add provider |
| GET | `/admin/v1/certificate-providers/{certificateProvider}` | — | Provider details |
| POST | `/admin/v1/certificate-providers/{certificateProvider}/edit` | — | Edit provider |
| DELETE | `/admin/v1/certificate-providers/{certificateProvider}/delete` | — | Delete provider |
| GET | `/admin/v1/certificate-providers/{certificateProvider}/certificates` | — | Provider certificates |
| POST | `/admin/v1/certificate-providers/{certificateProvider}/certificates/add` | — | Add certificate |
| GET | `/admin/v1/certificates/{certificate}` | — | Certificate details |
| POST | `/admin/v1/certificates/{certificate}/edit` | — | Edit certificate |
| DELETE | `/admin/v1/certificates/{certificate}/delete` | — | Delete certificate |
| GET | `/admin/v1/services` | — | List services |
| GET | `/admin/v1/services/{service}` | — | Service details |
| POST | `/admin/v1/services/{service}/approve` | — | Approve service |
| POST | `/admin/v1/services/{service}/toggle-hidden` | — | Toggle hidden |
| DELETE | `/admin/v1/services/{service}/delete` | — | Delete service |
| GET | `/admin/v1/orders` | Index Orders | List orders |
| GET | `/admin/v1/orders/pending` | Index Orders | Pending orders |
| GET | `/admin/v1/orders/in-progress` | Index Orders | In-progress orders |
| GET | `/admin/v1/orders/rejected` | Index Orders | Rejected orders |
| GET | `/admin/v1/orders/released` | Index Orders | Released orders |
| GET | `/admin/v1/orders/refunded` | Index Orders | Refunded orders |
| GET | `/admin/v1/orders/completed` | Index Orders | Completed orders |
| GET | `/admin/v1/orders/cancelled` | Index Orders | Cancelled orders |
| GET | `/admin/v1/orders/disputed` | Index Orders | Disputed orders |
| GET | `/admin/v1/orders/{order}` | Details Orders | Order details |
| POST | `/admin/v1/orders/{order}/cancel` | Cancel Orders | Cancel order |
| POST | `/admin/v1/orders/{order}/refund` | Refund Orders | Refund order |
| POST | `/admin/v1/orders/{order}/approve-cancellation` | Approve Order Cancellation | Approve cancellation |
| POST | `/admin/v1/orders/{order}/reject-cancellation` | Reject Order Cancellation | Reject cancellation |
| DELETE | `/admin/v1/orders/{order}/delete` | — | Delete order |
| GET | `/admin/v1/projects` | — | List projects |
| GET | `/admin/v1/projects/{project}` | — | Project details |
| POST | `/admin/v1/projects/{project}/approve-cancellation` | — | Approve project cancellation |
| POST | `/admin/v1/projects/{project}/reject-cancellation` | — | Reject project cancellation |
| GET | `/admin/v1/portfolios` | — | List portfolios |
| GET | `/admin/v1/portfolios/{portfolio}` | — | Portfolio details |
| POST | `/admin/v1/portfolios/{portfolio}/toggle-hidden` | — | Toggle hidden |
| DELETE | `/admin/v1/portfolios/{portfolio}/delete` | — | Delete portfolio |
| GET | `/admin/v1/chats` | — | List chats |
| POST | `/admin/v1/chats/open` | — | Open chat |
| GET | `/admin/v1/chats/{chat}` | — | Chat messages |
| POST | `/admin/v1/chats/{chat}/messages` | — | Send message |
| GET | `/admin/v1/kycs` | — | List KYCs |
| GET | `/admin/v1/kycs/approved` | — | Approved KYCs |
| GET | `/admin/v1/kycs/pending` | — | Pending KYCs |
| GET | `/admin/v1/kycs/rejected` | — | Rejected KYCs |
| GET | `/admin/v1/kycs/{kyc}` | — | KYC details |
| POST | `/admin/v1/kycs/{kyc}/approve` | — | Approve KYC |
| POST | `/admin/v1/kycs/{kyc}/reject` | — | Reject KYC |
| DELETE | `/admin/v1/kycs/{kyc}/delete` | — | Delete KYC |
| GET | `/admin/v1/payment-requests` | — | List payment requests |
| GET | `/admin/v1/payment-requests/approved` | — | Approved requests |
| GET | `/admin/v1/payment-requests/pending` | — | Pending requests |
| GET | `/admin/v1/payment-requests/rejected` | — | Rejected requests |
| GET | `/admin/v1/payment-requests/{paymentRequest}` | — | Request details |
| POST | `/admin/v1/payment-requests/{paymentRequest}/approve` | — | Approve request |
| POST | `/admin/v1/payment-requests/{paymentRequest}/reject` | — | Reject request |
| GET | `/admin/v1/wallets` | — | List wallets |
| GET | `/admin/v1/wallets/{wallet}/receipt` | — | Wallet receipt |
| GET | `/admin/v1/commissions` | — | List commissions |
| GET | `/admin/v1/commissions/{commission}` | — | Commission details |
| GET | `/admin/v1/commissions-settings` | — | List commission settings |
| GET | `/admin/v1/commissions-settings/{commissionSetting}` | — | Commission setting details |
| POST | `/admin/v1/commissions-settings/{commissionSetting}/edit` | — | Edit commission setting |
| DELETE | `/admin/v1/commissions-settings/{commissionSetting}/delete` | — | Delete commission setting |
| GET | `/admin/v1/refunds` | — | List refunds |
| GET | `/admin/v1/refunds/{refund}` | — | Refund details |
| GET | `/admin/v1/reports` | — | List reports |
| POST | `/admin/v1/reports/add` | — | Create report |
| GET | `/admin/v1/reports/{report}` | — | Report details |
| POST | `/admin/v1/reports/{report}/response` | — | Reply to report |
| POST | `/admin/v1/reports/{report}/toggle-status` | — | Toggle report status |
| DELETE | `/admin/v1/reports/{report}/delete` | — | Delete report |
| GET | `/admin/v1/features` | — | List features |
| POST | `/admin/v1/features/add` | — | Create feature |
| GET | `/admin/v1/features/{feature}` | — | Feature details |
| POST | `/admin/v1/features/{feature}/edit` | — | Edit feature |
| DELETE | `/admin/v1/features/{feature}/delete` | — | Delete feature |
| GET | `/admin/v1/banners` | — | List banners |
| POST | `/admin/v1/banners/add` | — | Create banner |
| GET | `/admin/v1/banners/{banner}` | — | Banner details |
| POST | `/admin/v1/banners/{banner}/edit` | — | Edit banner |
| POST | `/admin/v1/banners/{banner}/toggle-active` | — | Toggle banner active |
| DELETE | `/admin/v1/banners/{banner}/delete` | — | Delete banner |
| GET | `/admin/v1/faqs` | — | List FAQs |
| POST | `/admin/v1/faqs/add` | — | Create FAQ |
| GET | `/admin/v1/faqs/{faq}` | — | FAQ details |
| POST | `/admin/v1/faqs/{faq}/edit` | — | Edit FAQ |
| POST | `/admin/v1/faqs/{faq}/toggle-active` | — | Toggle FAQ active |
| DELETE | `/admin/v1/faqs/{faq}/delete` | — | Delete FAQ |
| GET | `/admin/v1/colors` | — | List colors |
| POST | `/admin/v1/colors/add` | — | Create color |
| GET | `/admin/v1/colors/{color}` | — | Color details |
| POST | `/admin/v1/colors/{color}/edit` | — | Edit color |
| DELETE | `/admin/v1/colors/{color}/delete` | — | Delete color |
| GET | `/admin/v1/pages` | — | List pages |
| GET | `/admin/v1/pages/{page}` | — | Page details |
| POST | `/admin/v1/pages/{page}/edit` | — | Edit page |
| GET | `/admin/v1/settings` | — | List settings |
| GET | `/admin/v1/settings/{setting}` | — | Setting details |
| POST | `/admin/v1/settings/{setting}/edit` | — | Edit setting |
| DELETE | `/admin/v1/settings/{setting}/delete` | — | Delete setting |
| POST | `/admin/v1/settings/withdrawal-settings` | — | Update withdrawal settings |
| GET | `/admin/v1/notification-settings` | — | List notification settings |
| POST | `/admin/v1/notification-settings/{notificationSetting}/toggle-active` | — | Toggle notification setting |
| GET | `/admin/v1/dashboard-notifications` | — | List dashboard notifications |
| POST | `/admin/v1/dashboard-notifications/add` | — | Create dashboard notification |
| GET | `/admin/v1/dashboard-notifications/{notification}` | — | Notification details |
| POST | `/admin/v1/dashboard-notifications/{notification}/edit` | — | Edit notification |
| POST | `/admin/v1/dashboard-notifications/{notification}/mark-as-seen` | — | Mark as seen |
| POST | `/admin/v1/dashboard-notifications/{notification}/mark-as-resolved` | — | Mark as resolved |
| DELETE | `/admin/v1/dashboard-notifications/{notification}/delete` | — | Delete notification |

### Example: Admin Authenticated Call

```bash
# 1. Login (web session, Fortify) — send CSRF + session cookies
curl -X POST "{{host}}/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "secret123"}' \
  -c cookies.txt

# 2. Then call admin endpoints with the session cookie
curl -X GET "{{host}}/admin/v1/orders" \
  -H "Accept: application/json" \
  -b cookies.txt
```

---

## 4. Other Routes

| Method | URI | Description |
| :--- | :--- | :--- |
| GET | `/dashboard` | Admin dashboard home (Inertia) |
| GET | `/login` / `POST /login` | Fortify login (web) |
| GET | `/register` / `POST /register` | Fortify register (web) |
| POST | `/logout` | Fortify logout (web) |
| GET | `/forgot-password` / `POST /forgot-password` | Fortify password reset (web) |
| GET | `/reset-password/{token}` / `POST /reset-password` | Fortify reset password (web) |
| GET | `/email/verify` | Email verification notice |
| POST | `/email/verification-notification` | Resend verification |
| GET | `/email/verify/{id}/{hash}` | Verify email |
| POST | `/webhook/myfatoorah` | MyFatoorah payment webhook |
| POST | `/api/v1/test/send-notification` | FCM test endpoint |

Passport OAuth2 routes are also registered under `/oauth/*` (`/oauth/token`, `/oauth/tokens`, `/oauth/clients`, ...).

---

## Quick Reference — Response Format

All API responses follow this shape:

```json
{
  "status": "success",          // or "error"
  "message": "...",             // optional
  "data": { ... }               // payload
}
```

Errors return the appropriate HTTP code (400/401/403/404/422/500) with `status: "error"`.