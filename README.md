<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License


The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).





# 💰 sTracker

A full-stack web application built with **Laravel 12** and **Vue 3 (Inertia.js)**. This tool allows users to track their income and expenses, visualize spending habits via charts, and manage their financial history.


## ✨ Features

* **Dashboard Summary:** Real-time calculation of Total Income, Total Expenses, and Current Balance.
* **Visual Analytics:** Dynamic chart showing income and expense breakdown by category using [Chart.js](https://www.chartjs.org/) (via Vue wrapper). Displays a responsive bar graph with optional pie views.
* **Full CRUD Functionality:**
   * **Create:** Add new transactions (Income/Expense).
   * **Read:** View a detailed history table of all entries and filter by category.
   * **Update:** Edit existing entries directly from the dashboard.
   * **Delete:** Remove transactions with automatic UI updates.
* **Authentication:** Secure login and registration powered by Laravel Breeze.
* **UI Framework:** Tailwind CSS enhanced with DaisyUI components for a clean, responsive look.


## 🚀 Tech Stack

* **Backend:** PHP 8.3, Laravel 12
* **Frontend:** Vue 3, Tailwind CSS
* **Bridge:** Inertia.js (Modern monolith approach)
* **Database:** MySQL
* **Charts:** Chart.js (via Vue wrapper) for dynamic dashboards

## 🛠️ Installation & Setup

Install Dependencies:

Bash
composer install
npm install
Environment Setup:

Copy .env.example to .env.

Configure your DB_DATABASE, DB_USERNAME, and DB_PASSWORD.

Run php artisan key:generate.

Database Migration:

```bash
php artisan migrate
```

Run the Application:

1. In one terminal:
   ```bash
   php artisan serve
   ```

2. In a second terminal (install front-end deps first):
   ```bash
   npm install
   npm run dev
   ```

Visit: http://localhost:8000


> 🔧 **Note:** Tailwind is configured with DaisyUI – if you add new Vue components make sure to rebuild assets (`npm run dev` or `composer run dev`).
