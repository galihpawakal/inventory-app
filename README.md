<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p> <p align="center"> <a href="#"><img src="https://img.shields.io/badge/status-development-orange" alt="Development Status"></a> <a href="#"><img src="https://img.shields.io/badge/license-MIT-brightgreen" alt="License"></a> </p>

InventoryApp

InventoryApp adalah aplikasi web sederhana untuk manajemen inventaris dan manajemen pengguna. Dibangun menggunakan Laravel Framework, aplikasi ini mempermudah pencatatan stok barang, transaksi penjualan, serta pengaturan hak akses pengguna.

Setup Lokal

Clone repository

git clone <https://github.com/galihpawakal/inventory-app.git>
cd inventory-app

Install dependencies
composer install
Copy file .env
cp .env.example .env


Generate App Key
php artisan key:generate

Database

Berdasarkan .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=root

Buat database inventory_db di MySQL:
CREATE DATABASE inventory_db;

Jalankan migration & seeder untuk membuat tabel dan data awal:
php artisan migrate --seed

Jalankan Server
php artisan serve

Akses aplikasi di http://127.0.0.1:8000


Fitur InventoryApp
1. Halaman Inventory – /products
- Menampilkan daftar produk beserta stok tersedia.
- Bisa menambah data produk baru (CRUD).
- Bisa mencatat penjualan:
-- Jumlah penjualan otomatis mengurangi stok produk.
-- Tidak boleh melebihi stok yang tersedia (validasi stock).
- Alert / validasi: Semua field wajib diisi saat tambah produk atau melakukan penjualan.

2️. Halaman Users – /users
- Menampilkan daftar user beserta role masing-masing.
- Bisa menambah data user baru:
- Validasi email, password minimal 8 karakter, role wajib dipilih.
- Bisa mengedit data user:
-- Mengubah role user.
-- Alert / validasi: Semua field wajib diisi saat tambah atau edit user.

