# Laravel Chat Application

A real-time private messaging application built with **Laravel 13**, **Livewire**, **Alpine.js**, and **Laravel Reverb** (WebSockets). Users can search for other members, start a conversation, and exchange messages instantly with live delivery and read-state updates — no page refresh required.

## Features

- Email/password authentication (Laravel Fortify)
- User search and conversation creation (Livewire)
- Real-time chat inbox — conversation list + message thread (Alpine.js + JSON API)
- Instant message delivery and new-conversation notifications via Reverb/Echo (private channels)
- Read receipts (mark conversation as read)
- Conversation deletion
- Modular architecture via `nwidart/laravel-modules` (chat feature lives in `app/Modules/Chat`)

## Tech Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.4, Laravel 13, Laravel Modules |
| Auth | Laravel Fortify |
| Frontend | Livewire, Flux UI, Alpine.js, Tailwind CSS 4, Vite |
| Realtime | Laravel Reverb, Laravel Echo, Sanctum (SPA auth for the JSON API) |
| Database | SQLite (default local), PostgreSQL (Docker) |
| Cache/Queue | Redis (Docker) / database driver (local) |
| Testing | Pest |

## Prerequisites

- PHP >= 8.4 with the extensions Laravel requires (`pdo_sqlite`/`pdo_pgsql`, `redis` optional)
- Composer 2
- Node.js 18+ and npm
- Or, if you'd rather skip local setup: Docker + Docker Compose

## Setup — Local (PHP/Composer)

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database** — the default `.env.example` uses SQLite:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```
   To use PostgreSQL/MySQL instead, update `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env` before migrating.

4. **Realtime (Reverb)** — add the following to `.env` (the example file ships without them):
   ```env
   BROADCAST_CONNECTION=reverb

   REVERB_APP_ID=chat-app
   REVERB_APP_KEY=local-key
   REVERB_APP_SECRET=local-secret
   REVERB_HOST=localhost
   REVERB_PORT=8000
   REVERB_SCHEME=http

   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

5. **Enable modules** (already enabled by default in `modules_statuses.json`, but if you add the app fresh):
   ```bash
   php artisan module:enable Chat
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   # module-specific assets (Chat's Alpine chat inbox JS/CSS)
   npm run build:modules
   ```

7. **Run everything** — starts the HTTP server, queue worker, and Vite dev server together:
   ```bash
   composer dev
   ```
   In a separate terminal, start the WebSocket server for real-time chat:
   ```bash
   php artisan reverb:start
   ```

8. Visit `http://localhost:8000`.

> One-shot alternative: `composer setup` runs install → `.env` copy → key generate → migrate → npm install → npm build (still requires `php artisan reverb:start` separately for realtime).

## Setup — Docker

The Docker stack runs the app (PHP-FPM), Nginx, PostgreSQL, Redis, Reverb, and a queue worker as separate services.

1. Copy `.env` and add the Reverb variables shown above (Docker Compose overrides `DB_HOST`, `REDIS_HOST`, and `REVERB_HOST` to the internal service names automatically).
2. Set `DB_CONNECTION=pgsql` in `.env` (the Postgres container expects `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` to be set).
3. Build and start the stack:
   ```bash
   docker compose up -d --build
   ```
   The `app` container's entrypoint (`docker/php/entrypoint.sh`) waits for Postgres, runs `php artisan migrate`, and builds frontend assets automatically on first boot.
4. Visit `http://localhost:8000`.

Useful commands:
```bash
docker compose logs -f app        # app logs
docker compose exec app php artisan tinker
docker compose exec app sh docker/php/migrate.sh   # run migrations manually
docker compose down                # stop the stack
```

## Running Tests

```bash
php artisan test --compact
# or a single file/filter
php artisan test --compact --filter=SomeTest
```

Static analysis and code style:
```bash
composer types:check   # phpstan
composer lint:check    # pint --test
```

## Demo Walkthrough

1. **Register** two accounts at `/register` (e.g. Alice and Bob) — Fortify handles auth, and `/dashboard` requires a verified session.
2. **Sign in as Alice** and go to `/chats` — a Livewire-powered search box (`FindUsers`) lets you look up other users by name/email.
3. **Start a conversation** — selecting Bob creates a `Conversation` + `Participant` rows and fires `ConversationCreated`, which is pushed to Bob in real time over a private Reverb channel if he's online.
4. **Open the inbox** at `/chat/inbox` — the conversation list and message thread are a hand-rolled Alpine.js component (`resources/assets/js/app.js`) talking to the versioned JSON API (`/api/v1/chat/...`) via Sanctum SPA-authenticated `fetch` calls.
5. **Send a message** — `POST /api/v1/chat/conversations/{conversation}/messages` persists the message and broadcasts `MessageSent` immediately (no queue delay — the event uses `ShouldBroadcastNow`).
6. **Sign in as Bob** in a second browser/incognito window — the new conversation and incoming messages appear live without refreshing, subscribed via Echo on the `conversation.{id}` private channel.
7. **Mark as read** — opening a conversation calls `PATCH /api/v1/chat/conversations/{conversation}/read` to update read state.
8. **Delete a conversation** — `DELETE /api/v1/chat/conversations/{conversation}` removes it from the inbox.

## Project Structure

Domain features live under `app/Modules/{Name}` (via `nwidart/laravel-modules`), each with its own `app/`, `routes/`, `resources/`, `database/`, and `tests/`. The chat feature is in `app/Modules/Chat`; cross-cutting auth/account scaffolding stays in the root `app/`. See `.ai/rules/` for the full set of architectural conventions used in this codebase.
