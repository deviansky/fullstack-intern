# Sistem Manajemen Tugas dan Pengguna

## Pendahuluan

Dokumen ini menyajikan deskripsi teknis mengenai implementasi sebuah sistem RESTful API yang dirancang untuk manajemen tugas dan pengguna. Sistem ini dilengkapi dengan antarmuka dasbor frontend yang fungsional.

Aplikasi ini dikembangkan sebagai pemenuhan **Tes Evaluasi Kemampuan Fullstack Developer**, dengan penekanan utama pada penerapan **Role-Based Access Control (RBAC)**, **logika bisnis yang terdefinisi**, serta **arsitektur perangkat lunak yang terstruktur**.

---

## 🛠️ Teknologi yang Digunakan

| Komponen     | Teknologi                             |
|--------------|----------------------------------------|
| Backend      | Laravel 11                             |
| Frontend     | HTML, Vanilla JavaScript, Bootstrap 5  |
| Database     | MySQL                                  |
| Autentikasi  | Laravel Sanctum                        |
---

## Fungsionalitas Sistem

### Fungsionalitas Backend

- **Otentikasi API:**  
  Implementasi mekanisme otentikasi yang aman untuk API menggunakan paket Laravel Sanctum.

- **Kontrol Akses Berbasis Peran (RBAC):**  
  Sistem mengimplementasikan tiga peran pengguna: `admin`, `manager`, dan `staff`, masing-masing dengan hak akses yang tergranulasi untuk setiap endpoint.

- **Middleware Kustom:**
  - `CheckUserStatus`: Middleware untuk memvalidasi dan memblokir permintaan dari pengguna dengan status nonaktif.
  - `LogRequest`: Middleware untuk mencatat setiap permintaan API yang masuk ke dalam basis data sebagai log aktivitas.

- **Penjadwal Tugas (Scheduler):**  
  Pemanfaatan Laravel Scheduler untuk mengeksekusi perintah Artisan secara periodik, yang berfungsi untuk memeriksa dan mencatat tugas yang telah melewati batas waktu.

- **Pengujian Perangkat Lunak:**
  - **Feature Tests:** Pengujian yang mencakup verifikasi fungsionalitas endpoint API, proses otentikasi, dan mekanisme otorisasi.
  - **Unit Tests:** Pengujian terisolasi untuk unit-unit logika bisnis yang kritikal.
---
## 📦 Instalasi & Setup

### 1. Clone Repository
```bash
git clone [[URL_REPOSITORY_ANDA]](https://github.com/deviansky/fullstack-intern.git)
cd fullstack-intern
```
### 2. Install Dependencies
```bash
npm install
```
### 3. Setup File Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```
### 4. Konfigurasi Database
Buka file .env dan sesuaikan bagian berikut:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_evaluasi_fullstack
DB_USERNAME=root
DB_PASSWORD=
```
### 5. Jalankan Migrasi & Seeder
```bash
php artisan migrate:fresh --seed
```
## ▶️ Menjalankan Aplikasi
### Jalankan Server Laravel
```bash
php artisan serve
```
---
## Diagram Relasi Entitas (ERD)

```mermaid
erDiagram
    USERS {
        UUID id PK
        string name
        string email
        string password
        enum role
        boolean status
    }
    TASKS {
        UUID id PK
        string title
        text description
        enum status
        date due_date
        UUID assigned_to FK
        UUID created_by FK
    }
    ACTIVITY_LOGS {
        UUID id PK
        string action
        text description
        datetime logged_at
        UUID user_id FK
    }

    USERS ||--o{ TASKS : "creates"
    USERS ||--o{ TASKS : "is_assigned"
    USERS ||--o{ ACTIVITY_LOGS : "performs"
```





---
## 📸 Dokumentasi Antarmuka Aplikasi

### 1. Halaman Login

Antarmuka login memungkinkan pengguna memasukkan email dan password untuk proses otentikasi menggunakan Laravel Sanctum. Jika berhasil login, token disimpan di `localStorage` dan digunakan untuk otorisasi selanjutnya.

**Fitur:**
- Input email dan password
- Validasi input secara instan
- Tampilan clean & responsif

---

### 2. Dasbor dan Manajemen Tugas (CRUD)

Dasbor utama menampilkan daftar tugas dalam bentuk kartu. Informasi yang ditampilkan mencakup:
- Judul tugas
- Deskripsi singkat
- Nama yang ditugaskan
- Batas waktu
- Status tugas (berwarna)

**Tombol Aksi:**
- **Edit**: Menampilkan modal penyuntingan
- **Hapus**: Konfirmasi sebelum menghapus
- Tombol aksi hanya muncul jika peran pengguna memiliki hak akses (`admin`, `manager`, atau `staff`)

---

### 3. Modal Penyuntingan Tugas

Ketika tombol "Edit" diklik, muncul modal yang memungkinkan pengguna memperbarui detail tugas.

**Fitur Modal:**
- Input judul, deskripsi, status, tanggal batas waktu
- Pilih pengguna yang ditugaskan
- Tombol simpan akan memanggil endpoint `PUT /api/tasks/{id}`
- Validasi form sebelum submit

---

### 4. Hasil Eksekusi Tes Otomatis

Setelah menjalankan perintah:
---
## Kredensial Login
- Admin: admin@example.com / password
- Manager: manager@example.com / password
- Staff: staff@example.com / password



