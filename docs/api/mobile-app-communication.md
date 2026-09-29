# Carport Monitor — Mobile API

HTTP JSON API between the Carport mobile app and the web monitor.

| Convention | Value |
|------------|-------|
| Base URL | `https://{monitor_host}/api` |
| Content-Type | `application/json` (all routes except attachment upload) |
| Device identity | Header `X-Mobile-Id: {uuid}` on all `POST …/sync` routes and attachment upload |

## Endpoints

| Method | Path | `X-Mobile-Id` | Purpose |
|--------|------|:-------------:|---------|
| `GET` | `/register` | — | Obtain device UUID (once per install) |
| `GET` | `/checkin/{updated_at}` | — | Verify sync + list web users for pairing |
| `POST` | `/vehicles/sync` | ✓ | Bulk upsert vehicles |
| `POST` | `/vehicles/{vehicle_id}/attachments` | ✓ | Upload a file attachment for a vehicle |
| `POST` | `/attachments/sync` | ✓ | Remove server attachments not present on the mobile |
| `POST` | `/service-items/sync` | ✓ | Bulk upsert service log entries |
| `POST` | `/permissions/sync` | ✓ | Set which web users may view this mobile's data |

**Typical flow:** `GET /register` → sync vehicles & service items → optional `POST /permissions/sync` to restrict viewers → `GET /checkin/{updated_at}`

`GET /register` grants every existing web user permission to view the new mobile's data. Use `POST /permissions/sync` only when you need to replace that default set (for example, to restrict or revoke access).

---

## `GET /register`

Obtain a persistent device UUID. Store it locally; send it as `X-Mobile-Id` on sync calls. Do not call again unless the ID is lost — each request creates a new mobile record.

```http
GET /api/register
```

**Response `200`**

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000"
}
```

---

## `POST /vehicles/sync`

Bulk create or update vehicles for the requesting mobile.

```http
POST /api/vehicles/sync
X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json
```

**Request body**

```json
{
  "vehicles": [
    {
      "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
      "name": "2019 Mazda CX-5",
      "mileage": 42350,
      "mileage_unit": "km",
      "description": "Soul Red Crystal Metallic",
      "user_manual_url": null,
      "service_manual_url": null,
      "maintenance_schedule_image": "iVBORw0KGgo...",
      "updated_at": "2026-07-08T10:15:30Z"
    }
  ]
}
```

| Field | Required | Notes |
|-------|:--------:|-------|
| `vehicles` | ✓ | Array of vehicle objects |
| `vehicles[].id` | ✓ | App UUID → stored as `external_id` |
| `vehicles[].name` | ✓ | Display name |
| `vehicles[].mileage` | ✓ | Integer ≥ 0 |
| `vehicles[].mileage_unit` | ✓ | `km` or `mi` |
| `vehicles[].description` | | Free text |
| `vehicles[].user_manual_url` | | String, max 2048 chars; `javascript:`, `data:`, and `vbscript:` schemes rejected |
| `vehicles[].service_manual_url` | | String, max 2048 chars; `javascript:`, `data:`, and `vbscript:` schemes rejected |
| `vehicles[].maintenance_schedule_image` | | Raw base64; omit to keep existing, `null` to clear |
| `vehicles[].updated_at` | ✓ | ISO 8601 datetime (second precision); stored as record `updated_at` |

**Response `200`** — [bulk sync shape](#bulk-sync-response)

**Upsert:** lookup by `vehicles[].id` → **create** (new) · **update** (same mobile) · **ignore** (owned by another mobile, no error)

---

## `POST /vehicles/{vehicle_id}/attachments`

Upload a generic file attachment (receipt, photo, PDF, etc.) for a vehicle owned by the requesting mobile. This is the **only** multipart route; all other API routes use JSON.

`{vehicle_id}` — the mobile vehicle UUID (`vehicles[].id` / `external_id`).

```http
POST /api/vehicles/a1b2c3d4-e5f6-7890-abcd-ef1234567890/attachments
X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: multipart/form-data
```

**Form fields**

| Field | Required | Notes |
|-------|:--------:|-------|
| `file` | ✓ | Binary upload; max 10 MB |
| `id` | ✓ | App UUID for the attachment → stored as `external_id` |

**Allowed file types:** JPEG, PNG, GIF, WebP, PDF.

**Example (`curl`)**

```bash
curl -X POST "https://{monitor_host}/api/vehicles/a1b2c3d4-e5f6-7890-abcd-ef1234567890/attachments" \
  -H "X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000" \
  -F "id=b2c3d4e5-f6a7-8901-bcde-f12345678901" \
  -F "file=@/path/to/oil-change-receipt.pdf"
```

**Response `201`**

```json
{
  "id": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
  "vehicle_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "original_filename": "oil-change-receipt.pdf",
  "mime_type": "application/pdf",
  "size": 48231,
  "created_at": "2026-07-09T08:30:00Z"
}
```

**Ownership:** the vehicle must exist and belong to the requesting mobile (`mobile_id` = `X-Mobile-Id`). Unassigned vehicles or vehicles owned by another mobile are rejected.

---

## `POST /attachments/sync`

Reconcile server-side attachments with the mobile's local set. The posted list is the authoritative set of attachment IDs that should remain on the server for this mobile; any other attachment owned by the requesting mobile is deleted (database record and stored file).

Use after local deletions on the mobile, or periodically, to keep the portal in sync without re-uploading unchanged files.

```http
POST /api/attachments/sync
X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json
```

**Request body**

```json
{
  "attachment_ids": [
    "b2c3d4e5-f6a7-8901-bcde-f12345678901",
    "c3d4e5f6-a7b8-9012-cdef-123456789012"
  ]
}
```

| Field | Required | Notes |
|-------|:--------:|-------|
| `attachment_ids` | ✓ | Must be present; may be `[]` to delete all attachments for this mobile |
| `attachment_ids[]` | ✓ | UUID; matches attachment `id` from upload / `external_id` on the server |

**Response `200`**

```json
{
  "deleted": 1,
  "attachment_ids": [
    "d4e5f6a7-b8c9-0123-def0-234567890123"
  ]
}
```

| Field | Type | Notes |
|-------|------|-------|
| `deleted` | integer | Count of attachments removed |
| `attachment_ids` | array | UUIDs of deleted attachments (`external_id`) |

**Diff semantics:** only attachments where `mobile_id` = `X-Mobile-Id` are considered. Attachments owned by other mobiles are never deleted. IDs in the posted list that do not exist on the server are ignored. Affected parent vehicles are touched so `GET /checkin/{updated_at}` reflects the change.

---

## `POST /service-items/sync`

Bulk create or update service log entries for the requesting mobile.

```http
POST /api/service-items/sync
X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json
```

**Request body**

```json
{
  "service_items": [
    {
      "id": "b2c3d4e5-f6a7-8901-bcde-f12345678901",
      "vehicle_id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
      "title": "OIL CHANGE",
      "description": "Full synthetic 0W-20",
      "mileage": 42350,
      "mileage_unit": "km",
      "occurred_at": "2026-03-04",
      "updated_at": "2026-07-08T10:15:30Z"
    }
  ]
}
```

| Field | Required | Notes |
|-------|:--------:|-------|
| `service_items` | ✓ | Array of service item objects |
| `service_items[].id` | ✓ | App UUID → stored as `external_id` |
| `service_items[].vehicle_id` | ✓ | Matches `vehicles[].id` / `external_id` |
| `service_items[].title` | ✓ | |
| `service_items[].description` | | |
| `service_items[].mileage` | ✓ | Integer ≥ 0 |
| `service_items[].mileage_unit` | ✓ | `km` or `mi` |
| `service_items[].occurred_at` | ✓ | Date `YYYY-MM-DD` |
| `service_items[].updated_at` | ✓ | ISO 8601 datetime (second precision); stored as record `updated_at` |

**Response `200`** — [bulk sync shape](#bulk-sync-response)

**Upsert:** lookup by `service_items[].id` → **create** (vehicle exists and belongs to same mobile) · **update** (same mobile) · **ignore** (other mobile, or vehicle missing/not owned)

---

## `POST /permissions/sync`

Replace the full set of web users allowed to view this mobile's vehicles and service items on the monitor.

```http
POST /api/permissions/sync
X-Mobile-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json
```

**Request body**

```json
{
  "user_ids": [1, 2]
}
```

| Field | Required | Notes |
|-------|:--------:|-------|
| `user_ids` | ✓ | Must be present; may be `[]` to revoke all |
| `user_ids[]` | ✓ | Integer; must exist in `users.id` |

**Response `200`**

```json
{
  "synced": 2,
  "user_ids": [1, 2]
}
```

Full replace semantics: posted array becomes the complete permission set. Empty array revokes all viewers.

---

## `GET /checkin/{updated_at}`

After syncing, verify the portal has caught up with the mobile's local data and fetch web users for session selection.

`{updated_at}` — the mobile app's aggregate last-updated time (max `updated_at` across all local vehicles, service items, and attachments), as an ISO 8601 datetime with second precision. URL-encode characters such as `:` and `+` in the path segment.

```http
GET /api/checkin/2026-07-08T10%3A15%3A30Z
```

**Response `200`**

```json
{
  "users": {
    "1": "Alex Morgan",
    "2": "Jordan Lee"
  },
  "isDatabaseSync": true
}
```

| Field | Type | Notes |
|-------|------|-------|
| `users` | object | Map of user `id` (string key) → display `name` |
| `isDatabaseSync` | boolean | `true` when mobile `updated_at` is not later than the portal's latest `updated_at` |

The portal compares against the maximum `updated_at` across **all** vehicles, service items, and attachments in the database.

| Mobile `updated_at` | Portal max `updated_at` | `isDatabaseSync` |
|---------------------|-------------------------|------------------|
| `10:15:30` | `10:15:30` | `true` |
| `10:15:31` | `10:15:30` | `false` (mobile ahead — sync needed) |
| `10:15:29` | `10:15:30` | `true` (portal caught up or ahead) |
| any | no records | `false` |

`users` is always returned. Malformed datetime → `404`.

---

## Bulk sync response

Returned by `POST /vehicles/sync` and `POST /service-items/sync`.

```json
{
  "created": 1,
  "updated": 0,
  "ignored": 0,
  "items": [
    { "id": "a1b2c3d4-e5f6-7890-abcd-ef1234567890", "status": "created" }
  ]
}
```

`items[].status`: `created` · `updated` · `ignored`

---

## Errors

| Status | When |
|--------|------|
| `404` | `GET /checkin/{updated_at}` with invalid datetime format; unknown `vehicle_id` on attachment upload |
| `403` | Attachment upload when vehicle is not owned by the requesting mobile |
| `422` | Missing/invalid `X-Mobile-Id`, unknown `user_ids[]`, invalid `attachment_ids[]`, or request validation failure |

### Attachment upload validation errors (`422`)

Attachment upload returns custom, mobile-actionable messages:

| Field | Rule | Message |
|-------|------|---------|
| `mobile_id` | required | `The X-Mobile-Id header is required.` |
| `mobile_id` | uuid | `The X-Mobile-Id header must be a valid UUID.` |
| `mobile_id` | exists | `The X-Mobile-Id header does not match a registered mobile device.` |
| `id` | required | `The attachment id is required.` |
| `id` | uuid | `The attachment id must be a valid UUID.` |
| `id` | unique | `An attachment with this id already exists.` |
| `file` | required | `The attachment file is required.` |
| `file` | file | `The attachment must be a valid uploaded file.` |
| `file` | max | `The attachment file must not exceed 10 MB.` |
| `file` | mimes | `The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF.` |

Example `422` response:

```json
{
  "message": "The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF.",
  "errors": {
    "file": ["The attachment file must be a JPEG, PNG, GIF, WebP image, or PDF."]
  }
}
```

### Attachment upload authorization error (`403`)

```json
{
  "message": "This vehicle is not owned by the requesting mobile device."
}
```

### General validation errors

Validation errors on JSON routes use Laravel's standard shape:

```json
{
  "message": "The mobile id field is required.",
  "errors": {
    "mobile_id": ["The mobile id field is required."]
  }
}
```
