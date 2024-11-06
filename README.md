# API Test Assessment

API ini dikembangkan sebagai bagian dari assessment untuk keperluan testing. API ini menyediakan beberapa endpoint yang berfungsi untuk CRUD data user dan order, beserta dengan autentikasi menggunakan JWT/Bearer Token.

## Fitur Utama

-   **CRUD** Data User, order
-   **Sistem Otentikasi** dengan JWT/Bearer Token
-   **Validasi** permintaan menggunakan request validation
-   Dokumentasi tersedia di **Swagger** (lihat link di bawah)

## Default User

Setelah menjalankan migration dan seeder, gunakan kredensial berikut untuk login:

-   Email: superadmin@email.com
-   Password: superadmin

## Prasyarat

-   **PHP** 8.0 atau lebih baru
-   **Composer** 2.x
-   **MySQL**
-   **Laravel** 10 ++

## Cara Clone Repository

Ikuti langkah-langkah berikut untuk mengkloning dan menjalankan proyek ini secara lokal:

1. **Clone repositori ini**:

    ```bash
    git clone https://github.com/rezihandianto/api-datatech
    cd nama-repositori
    ```

2. **Install dependencies** dengan Composer:

    ```bash
    composer install
    ```

3. **Salin file environment**:

    ```bash
    cp .env.example .env
    ```

4. **Konfigurasi file .env**:

    - Atur koneksi database di `.env`:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=nama_database
        DB_USERNAME=root
        DB_PASSWORD=password
        ```

5. **Generate application key**:

    ```bash
    php artisan key:generate
    ```

6. **Jalankan migrasi dan seeders** :

    ```bash
    php artisan migrate --seed
    ```

7. **Jalankan generate JWT**:

    ```bash
    php artisan jwt:secret
    ```

8. **Jalankan server lokal**:

    ```bash
    php artisan serve
    ```

    Akses API melalui `http://127.0.0.1:8000`.

## Dokumentasi API

Dokumentasi API dapat diakses melalui Swagger setelah server berjalan. Silakan buka link berikut di browser Anda:

[http://127.0.0.1:8000/docs](http://127.0.0.1:8000/docs)

Dokumentasi ini mencakup seluruh endpoint yang tersedia beserta contoh permintaan dan tanggapan.

## Testing

Untuk menjalankan testing, gunakan perintah berikut:

```bash
php artisan test
```
