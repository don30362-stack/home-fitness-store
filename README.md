# Home Fitness Store

家用重訓器材品牌電商網站。

本專案採前後端分離架構，使用 Vue 建立前端 SPA，Laravel 提供 REST API，並使用 MySQL 儲存系統資料。

## Tech Stack

### Frontend

* Vue 3
* Vite
* Vue Router
* Pinia
* Bootstrap 5
* JavaScript

### Backend

* Laravel 13
* PHP 8.4
* Laravel Sanctum
* REST API

### Database

* MySQL

### Development Tools

* Node.js 24 LTS
* npm
* Composer
* Git
* GitHub
* XAMPP

## Project Structure

```text
home-fitness-store/
├── frontend/    # Vue frontend
└── backend/     # Laravel backend
```

## Local Development

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Laravel development server:

```text
http://127.0.0.1:8000
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Vue development server:

```text
http://localhost:5173
```

## API

Development API base URL:

```text
http://127.0.0.1:8000/api
```

## Status

Project environment and frontend/backend architecture have been initialized. Core e-commerce features are under development.
