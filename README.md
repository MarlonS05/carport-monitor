# Carport Monitor

Carport Monitor is a Laravel web dashboard for viewing vehicle information and
service history synchronized from the Carport mobile app. It provides a
permission-aware monitor for vehicle data, maintenance records, and uploaded
vehicle documents.

**Open the local Carport app:** [http://localhost:8000](http://localhost:8000)

## Features

- Dashboard for browsing synchronized vehicles.
- User selection and authenticated monitor access.
- Vehicle detail pages with mileage, manuals, maintenance information, and attachments.
- Service history with dates, mileage, and descriptions.
- Mobile API for device registration, check-ins, vehicle synchronization, service-item synchronization, and permissions.
- Vehicle attachment uploads and secure downloads for images and PDFs.
- SQLite database for local development and automated tests.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite with the PHP SQLite extension

## Setup

From the project directory:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

The default configuration uses SQLite, database-backed sessions, cache, and
queues. The commands above create and migrate the local database. Keep the
generated `.env` file out of version control.

## Run locally

Start the Laravel server and Vite development server in separate terminals:

```bash
php artisan serve --host=0.0.0.0 --port=8000
npm run dev
```

Then open [http://localhost:8000](http://localhost:8000).

### Android Studio emulator

When accessing the local app from an Android Studio emulator, use
`10.0.2.2:8000` instead of `localhost:8000` or `127.0.0.1:8000`. The server
must listen on all interfaces as shown in the startup command above:

```text
http://10.0.2.2:8000
```

## Testing

Run the complete automated test suite with:

```bash
php artisan test
```

Tests use an in-memory SQLite database and do not require a separate database
server.

## Mobile API

The HTTP API used by the Carport mobile app is documented in
[`docs/api/mobile-app-communication.md`](docs/api/mobile-app-communication.md).
When running locally, its base URL is
`http://localhost:8000/api`, or `http://10.0.2.2:8000/api` from an Android
Studio emulator.
