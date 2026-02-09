# 💬 Laravel Live Chat with Reverb

A real-time live chat application built with **Laravel**, **Laravel Reverb** (WebSocket server), and **Laravel Echo** (JavaScript client). Users can register, log in, and chat in real-time — messages appear instantly on all connected clients without page refresh.

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)
![Reverb](https://img.shields.io/badge/Reverb-WebSockets-blue)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 🏗️ How It Works

### Architecture Overview

```
Browser (User A)                         Browser (User B)
     │                                        │
     │  POST /chat/send                       │
     ▼                                        │
┌─────────────┐     broadcast event     ┌─────────────┐
│   Laravel    │ ──────────────────────► │   Reverb    │
│   Server     │                        │  WebSocket  │
│  (port 8000) │                        │  (port 8080)│
└─────────────┘                         └──────┬──────┘
                                               │
                                    pushes to all clients
                                               │
                                        ┌──────┴──────┐
                                        │ Laravel Echo │
                                        │  (JS Client) │
                                        └─────────────┘
                                               │
                                     Updates chat UI instantly
                                     for User A & User B
```

### Flow

1. **User sends a message** → `POST /chat/send` hits `ChatController@sendMessage`
2. **Controller saves the message** to the database via the `Message` model
3. **Controller broadcasts** a `MessageSent` event via Laravel Broadcasting
4. **Laravel Reverb** (WebSocket server) receives the event and pushes it to all connected clients on the `chat` channel
5. **Laravel Echo** (JavaScript) listens on the `chat` channel and appends the new message to the DOM in real-time

### Key Components

| Component | File | Purpose |
|-----------|------|---------|
| **Message Model** | `app/Models/Message.php` | Eloquent model with `user` relationship |
| **MessageSent Event** | `app/Events/MessageSent.php` | Broadcast event implementing `ShouldBroadcastNow` |
| **ChatController** | `app/Http/Controllers/ChatController.php` | Handles chat page rendering & message sending |
| **Routes** | `routes/web.php` | Auth routes (register/login/logout) & chat routes |
| **Chat View** | `resources/views/chat.blade.php` | Chat UI with Echo listener for real-time updates |
| **Echo Config** | `resources/js/echo.js` | Laravel Echo + Pusher.js configured for Reverb |
| **Broadcasting Config** | `config/broadcasting.php` | Reverb connection settings |

---

## 🚀 Getting Started

### Prerequisites

- **PHP** >= 8.2
- **Composer**
- **Node.js** >= 18 & npm
- **SQLite** (default) or any other database

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/Laravel-Reverb.git
   cd Laravel-Reverb
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure the `.env` file** — add/update these Reverb settings:
   ```env
   BROADCAST_CONNECTION=reverb

   REVERB_APP_ID=my-app-id
   REVERB_APP_KEY=my-app-key
   REVERB_APP_SECRET=my-app-secret
   REVERB_HOST=localhost
   REVERB_PORT=8080
   REVERB_SCHEME=http

   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

   > Also change `BROADCAST_CONNECTION=log` to `BROADCAST_CONNECTION=reverb` if it appears earlier in the file.

6. **Create the database and run migrations**
   ```bash
   php artisan migrate
   ```
   When prompted to create the SQLite database, type `yes`.

7. **Build frontend assets**
   ```bash
   npm run build
   ```

---

## ▶️ Running the Application

You need **2 terminals** running simultaneously:

### Terminal 1 — Start the Laravel server
```bash
php artisan serve
```
> Runs on `http://127.0.0.1:8000`

### Terminal 2 — Start the Reverb WebSocket server
```bash
php artisan reverb:start
```
> Runs on `ws://0.0.0.0:8080`

### (Optional) Terminal 3 — Vite dev server for hot-reload during development
```bash
npm run dev
```

---

## 🧪 Usage

1. Open `http://127.0.0.1:8000/register` in your browser
2. Create a new account
3. You'll be redirected to the **Chat Room**
4. Open a **second browser window** (or incognito) and register another user
5. Start chatting — messages appear **instantly** on both screens! 🎉

---

## 📁 Project Structure

```
Laravel-Reverb/
├── app/
│   ├── Events/
│   │   └── MessageSent.php          # Broadcast event
│   ├── Http/Controllers/
│   │   └── ChatController.php       # Chat logic
│   └── Models/
│       ├── Message.php              # Message model
│       └── User.php                 # User model
├── config/
│   └── broadcasting.php             # Broadcasting config
├── database/
│   └── migrations/                  # Users & messages tables
├── resources/
│   ├── js/
│   │   ├── app.js                   # Main JS entry
│   │   ├── bootstrap.js             # Axios + Echo import
│   │   └── echo.js                  # Laravel Echo config for Reverb
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php        # Base layout
│       ├── auth/
│       │   ├── login.blade.php      # Login page
│       │   └── register.blade.php   # Registration page
│       └── chat.blade.php           # Chat room UI
├── routes/
│   ├── channels.php                 # Broadcast channel auth
│   └── web.php                      # Web routes
├── .env.example                     # Environment template
├── composer.json
├── package.json
└── vite.config.js
```

---

## 🛠️ Technologies

- **[Laravel 12](https://laravel.com/)** — PHP web framework
- **[Laravel Reverb](https://reverb.laravel.com/)** — First-party WebSocket server for Laravel
- **[Laravel Echo](https://github.com/laravel/echo)** — JavaScript library for subscribing to channels and listening for events
- **[Pusher.js](https://github.com/pusher/pusher-js)** — WebSocket client (used by Echo for Reverb connections)
- **[Vite](https://vite.dev/)** — Frontend build tool
- **SQLite** — Lightweight database (default)

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
