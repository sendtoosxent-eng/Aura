# AfyaLink Mobile API v1

Base path: `/api/v1`

Send `Accept: application/json` on every request. Authenticated routes require:

```http
Authorization: Bearer <access_token>
```

Validation failures use Laravel's standard `422` response with an `errors` object.

## Local iOS setup

Prepare the local database and create patient/provider accounts:

```bash
php artisan migrate
php artisan db:seed --class=MobileDemoSeeder
php artisan serve --host=0.0.0.0 --port=8000
```

The iOS Simulator defaults to `http://127.0.0.1:8000/api/v1/`. For a physical
iPhone, add the `AFYALINK_API_BASE_URL` scheme environment variable using the
Mac's LAN address, for example `http://192.168.1.20:8000/api/v1/`.

Demo logins (all use `Password123!`):

- Patient: `+256700555666`
- Facility: `+256700111222`
- Professional: `+256700333444`

The first login from a device requires the six-digit OTP returned as
`debug_code` while the backend is running locally or with debug enabled.

### Sending real OTP messages

Set the following values in `.env` using an Africa’s Talking application:

```dotenv
AFYALINK_SMS_DRIVER=africastalking
AFRICASTALKING_USERNAME=your_username
AFRICASTALKING_API_KEY=your_api_key
AFRICASTALKING_SENDER_ID=
```

For the Africa’s Talking sandbox, keep the username as `sandbox` and use a
sandbox API key. Sandbox messages only reach phone numbers registered in the
sandbox simulator. For live Ugandan numbers, use live application credentials
and an approved sender ID when one has been assigned. Leave `SENDER_ID` empty
until it is approved. After changing `.env`, run `php artisan optimize:clear`.

Use `AFYALINK_SMS_DRIVER=log` when developing without SMS credits. In that
mode, codes are written to `storage/logs/laravel.log` and are never sent.

## Authentication

### Look up an account

`POST /auth/lookup`

```json
{
  "phone": "+256700111222",
  "role": "patient",
  "device_id": "stable-keychain-device-id"
}
```

`role` is `patient` or `provider`. The response contains the safe profile preview and
`requires_security_otp`.

### Password login

`POST /auth/login`

```json
{
  "phone": "+256700111222",
  "password": "Strong!Pass1",
  "role": "patient",
  "device_id": "stable-keychain-device-id",
  "device_name": "Musa's iPhone"
}
```

A trusted IP/device returns an access token immediately. A new IP/device returns HTTP `202`,
`challenge_id`, and requires the next endpoint.

### Verify a new login context

`POST /auth/login/verify`

```json
{
  "challenge_id": 12,
  "otp": "123456",
  "device_id": "stable-keychain-device-id",
  "device_name": "Musa's iPhone"
}
```

### Resend a login code

`POST /auth/login/resend`

```json
{
  "challenge_id": 12,
  "device_id": "stable-keychain-device-id"
}
```

The resend endpoint has a 60-second cooldown and returns a new `challenge_id`.

### Current account and logout

- `GET /auth/me`
- `POST /auth/logout`

## Provider registration

- `GET /provider-registration/facilities?q=mulago` — verified workplace search.
- `POST /provider-registration/identity` — multipart identity/account step; returns a Sanctum token.
- `GET /provider-registration/status` — current application and uploaded document metadata.
- `POST /provider-registration/email/resend`
- `POST /provider-registration/phone/send`
- `POST /provider-registration/phone/verify` with `{ "otp": "123456" }`.
- `POST /provider-registration/application` — multipart regulatory details and documents.

Identity uses the same field names as the web flow:

`application_type`, `healthcare_provider_id`, `first_name`, `middle_name`, `last_name`,
`date_of_birth`, `sex`, `nationality`, `profile_photo`, `phone`, `email`, `nin`, `password`,
`password_confirmation`, and `device_name`.

Application submission uses nested multipart names such as `regulatory[name]`,
`regulatory[services][]`, `documents[operating_licence]`, and `declaration`.

## Discovery and maps

- `GET /providers`
- `GET /providers/{id}`
- `GET /doctors`
- `GET /doctors/{id}`

Provider filters: `q`, `type`, `district`, `service`, `emergency`, `open_24_hours`,
`min_rating`, `map_only`, and `per_page`.

Doctor filters: `q`, `specialty`, `district`, `provider_id`, and `per_page`.

Doctor responses include their facility's latitude and longitude for map annotations.

## Appointments

Authenticated endpoints:

- `GET /appointments`
- `POST /appointments`
- `PATCH /appointments/{id}` — provider changes status to `confirmed`, `cancelled`, or `completed`.
- `POST /appointments/{id}/cancel` — patient cancellation.

Create request:

```json
{
  "healthcare_provider_id": 1,
  "doctor_id": 4,
  "appointment_date": "2026-10-20",
  "appointment_time": "10:30",
  "appointment_type": "in_person",
  "reason": "Follow-up consultation"
}
```

If both facility and doctor are provided, the API verifies that the doctor belongs to that facility.
