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

```bash
composer install
npm install
```

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
