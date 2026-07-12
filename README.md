# Bookshop Management System

A full-stack online bookshop built with Laravel, MySQL, and Vanilla JavaScript.

## Key Features

**Customer:**
- Browse, Search & Filter books with AJAX
- Shopping Cart with Physical / E-book format selector
- Wishlist with quantity controls
- Checkout with Stripe, KPay, Wave Pay, Cash on Delivery
- Real-time Notifications
- Profile Management

**Admin:**
- Dashboard with period stats (Week / Month / Year)
- Inventory Management with 6 availability states
- Order Management with status flow
- Bulk Inventory Update
- Promotion Email System
- AI Assistant for book descriptions
- Role-based Permissions

---

## Setup

git clone https://github.com/thevamnonstop123-dotcom/Bookshop.git
cd Bookshop
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve

Visit: http://127.0.0.1:8000

---

## Demo Accounts

| Role | URL | Email | Password |
|------|-----|-------|----------|
| Admin | /admin/login | admin@bookshop.com | password |
| Customer | /register | (Register new) | - |


## Environment Setup

Copy .env.example to .env and fill in:

- Stripe keys: https://dashboard.stripe.com/apikeys
- Google OAuth: https://console.cloud.google.com
- Groq AI: https://console.groq.com

---

Developed by Thomas Nyan
