<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# 📚 Perjalanan Belajar Laravel

Repositori ini adalah catatan pribadi dari perjalanan saya dalam mempelajari framework **Laravel**. Di dalamnya berisi cuplikan kode, proyek-proyek kecil, dan catatan yang mencakup berbagai topik dan fitur Laravel. Tujuan saya adalah untuk menggunakan ini sebagai referensi dan juga sebagai bukti kemajuan saya.

## 📝 Isi Repositori

Setiap folder dalam repositori ini didedikasikan untuk topik atau proyek tertentu:

-   **`lara-crud-app/`**: Konsep dasar membuat Create, Read, Update dan Delete.


---

## 🛠️ Persyaratan

Untuk menjalankan proyek dan kode di repositori ini, Anda perlu memiliki beberapa hal berikut yang sudah terinstal di komputer Anda:

-   **PHP** (versi 8.1 atau yang lebih tinggi)
-   **Composer**
-   **Node.js** & **npm** (untuk aset front-end)
-   **Sistem database** (misalnya, MySQL, PostgreSQL, SQLite)

---

## 🚀 Cara Menjalankan Kode

Untuk mencoba salah satu proyek atau contoh kode:

1.  **Clone repositori ini:**
    ```bash
    git clone [https://github.com/your-username/laravel-learning-journey.git](https://github.com/your-username/laravel-learning-journey.git)
    cd laravel-learning-journey
    ```
2.  **Masuk ke folder proyek:**
    ```bash
    cd projects/nama-proyek-anda
    ```
3.  **Instal dependensi:**
    ```bash
    composer install
    npm install
    ```
4.  **Siapkan environment:**
    -   Buat file `.env` dari file `.env.example`: `cp .env.example .env`
    -   Buat kunci aplikasi: `php artisan key:generate`
    -   Konfigurasikan kredensial database Anda di file `.env`.
5.  **Jalankan migrasi:**
    ```bash
    php artisan migrate
    ```
6.  **Jalankan aplikasi:**
    ```bash
    php artisan serve
    ```
    Anda kemudian dapat mengakses aplikasi di `http://127.0.0.1:8000`.

---

## 🤝 Kontribusi

Repositori ini bersifat pribadi, tetapi jangan ragu untuk membuka _issue_ atau _pull request_ jika Anda melihat ada cara untuk meningkatkan kode atau konsep. Semua saran sangat diterima!
