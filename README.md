# IntelliFlow — AI Automation Platform

Enterprise-grade AI automation SaaS for intelligent workflows, copilots, analytics, and business operations.

## Stack

| Layer | Technology |
|---|---|
| Frontend | Nuxt 3, TypeScript, Tailwind CSS, Pinia, Vue-ChartJS |
| Backend | Laravel 11, PHP 8.3 |
| Database | PostgreSQL via Supabase |
| AI | OpenAI API, Embeddings, Semantic Search |
| Auth | Laravel Sanctum + Supabase Auth |

## Structure

```
intelliflow/
├── frontend/   # Nuxt 3 SPA
└── backend/    # Laravel REST API
```

## Setup

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## Environment Variables

See `frontend/.env.example` and `backend/.env.example` for required variables.
