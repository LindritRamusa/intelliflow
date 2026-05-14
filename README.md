# IntelliFlow — Enterprise AI Automation Platform

An enterprise-grade AI-powered SaaS platform that automates workflows, provides intelligent copilots, delivers business analytics, and streamlines operations across teams.

## Stack

| Layer | Technology |
|---|---|
| Frontend | Nuxt 3, TypeScript, Tailwind CSS, Pinia, Vue-ChartJS |
| Backend | Laravel 13, PHP 8.3, Laravel Sanctum |
| Database | PostgreSQL |
| AI | OpenAI GPT-4o (server-side via Nuxt server routes) |
| Cache/Queue | Redis |

## Features

- **AI Copilot** — GPT-4o powered enterprise assistant with context-aware responses
- **Workflow Automation** — Trigger-based workflow builder with CRUD management
- **Automations Engine** — Rule-based automation execution with toggle controls
- **Business Analytics** — Real-time charts, KPI cards, and activity tracking
- **Recruitment AI** — Candidate pipeline with AI CV analysis and scoring
- **Knowledge Base** — Company documentation with semantic search
- **Notification System** — Real-time alerts with read/unread management
- **Multi-tenant Auth** — Organization-scoped data with role-based access

## Project Structure

```
intelliflow/
├── frontend/              # Nuxt 3 SPA
│   ├── pages/             # Route pages (auth + dashboard)
│   ├── components/        # Reusable UI components
│   ├── composables/       # Data fetching + business logic
│   ├── stores/            # Pinia state (auth, notifications)
│   ├── layouts/           # default.vue + dashboard.vue
│   ├── middleware/        # auth + guest guards
│   ├── plugins/           # auth init + chart.js registration
│   └── server/api/        # Nuxt server routes (AI proxy)
└── backend/               # Laravel REST API
    ├── app/Http/Controllers/
    ├── app/Models/
    ├── database/migrations/
    └── routes/api.php
```

## Setup

### Prerequisites
- PHP 8.3+, Composer
- Node.js 20+, npm
- PostgreSQL
- Redis (optional for cache/queues)
- OpenAI API key

### Backend

```bash
cd backend
composer install
cp .env.example .env
# Edit .env: set DB credentials, APP_KEY, OPENAI_API_KEY
php artisan key:generate
php artisan migrate
php artisan serve
# Runs at http://localhost:8000
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
# Edit .env: set NUXT_PUBLIC_API_BASE and OPENAI_API_KEY
npm run dev
# Runs at http://localhost:3000
```

## API Routes

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me

GET    /api/dashboard/stats

GET    /api/workflows
POST   /api/workflows
GET    /api/workflows/{id}
PUT    /api/workflows/{id}
DELETE /api/workflows/{id}
PATCH  /api/workflows/{id}/toggle

GET    /api/automations
POST   /api/automations
PUT    /api/automations/{id}
DELETE /api/automations/{id}
PATCH  /api/automations/{id}/toggle

POST   /api/ai/logs
GET    /api/ai/logs

GET    /api/notifications
PATCH  /api/notifications/read-all
PATCH  /api/notifications/{id}/read

GET    /api/analytics/overview
GET    /api/analytics/workflows
GET    /api/analytics/activity
```

## Environment Variables

### Backend `.env`
```
DB_CONNECTION=pgsql
DB_DATABASE=intelliflow
SANCTUM_STATEFUL_DOMAINS=localhost:3000
OPENAI_API_KEY=sk-...
```

### Frontend `.env`
```
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
OPENAI_API_KEY=sk-...
```
