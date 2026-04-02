# AKOM (Intelligence Reporting System)

[![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Tailwind 4](https://img.shields.io/badge/Tailwind-4-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?style=flat-square&logo=postgresql)](https://www.postgresql.org)

**AKOM** adalah sistem pelaporan intelijen ekonomi premium yang dirancang untuk menghasilkan ringkasan eksekutif (whitepaper) dengan visualisasi data tingkat tinggi dan sistem distribusi tautan unik yang aman.

## ✨ Fitur Utama

-   **Intelligence Brief UI**: Tampilan laporan dengan estetika "Top Secret" yang premium, tipografi tajam, dan visualisasi data SVG kustom.
-   **Sistem Link Unik (UUID)**: Setiap laporan memiliki tautan acak yang aman untuk dibagikan tanpa perlu login publik.
-   **Admin Command Center**: Dashboard lengkap untuk mengelola laporan (CRUD) dengan input data dinamis untuk bab-bab laporan, poin analisis, dan statistik.
-   **Role-Based Access Control (RBAC)**: Sistem manajemen user dengan peran Superadmin dan Admin.
-   **Visualisasi Dinamis**: Modul grafik SVG otomatis untuk harga komoditas (Brent Oil) dan bar tekanan fiskal (APBN Stress Test).

## 🛠 Tech Stack

-   **Backend**: Laravel 13 (PHP 8.4+)
-   **Frontend**: Vue 3 + Inertia.js (Modern Monolith)
-   **Styling**: Tailwind CSS v4
-   **Database**: PostgreSQL
-   **State Management**: Vue Composition API + Inertia Forms

## 🚀 Instalasi

1.  **Kloning repositori**:
    ```bash
    git clone https://github.com/iwanharli/akom.git
    cd akom
    ```

2.  **Instansi Dependensi**:
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Database**:
    Salin `.env.example` ke `.env` dan sesuaikan kredensial PostgreSQL Anda:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=db_akom
    DB_USERNAME=postgres
    DB_PASSWORD=
    ```

4.  **Migrasi & Seed**:
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Kompilasi Asset**:
    ```bash
    npm run dev
    # atau untuk produksi
    npm run build
    ```

6.  **Jalankan Server**:
    ```bash
    php artisan serve
    ```

## 🔐 Akun Demo (Default)

-   **Superadmin**: `superadmin@example.com` (password: `password`)
-   **Admin**: `admin@example.com` (password: `password`)

## 📜 Lisensi
© 2026 Intelligence Reporting System. All rights reserved.
