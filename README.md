Sistem Manajemen Tugas dan PenggunaPendahuluanDokumen ini menyajikan deskripsi teknis mengenai implementasi sebuah sistem RESTful API yang dirancang untuk manajemen tugas dan pengguna. Sistem ini dilengkapi dengan antarmuka dasbor frontend yang fungsional. Aplikasi ini dikembangkan sebagai pemenuhan Tes Evaluasi Kemampuan Fullstack Developer, dengan penekanan utama pada penerapan Role-Based Access Control (RBAC), logika bisnis yang terdefinisi, serta arsitektur perangkat lunak yang terstruktur.Platform Backend: Laravel 11Platform Frontend: Vanilla JavaScript, HTML, Bootstrap 5Basis Data: MySQLMekanisme Otentikasi: Laravel SanctumFungsionalitas SistemFungsionalitas BackendOtentikasi API: Implementasi mekanisme otentikasi yang aman untuk API menggunakan paket Laravel Sanctum.Kontrol Akses Berbasis Peran (RBAC): Sistem mengimplementasikan tiga peran pengguna (admin, manager, staff), masing-masing dengan hak akses yang tergranulasi untuk setiap endpoint.Manajemen Pengguna: Fungsionalitas CRUD (Create, Read, Update, Delete) untuk entitas pengguna, yang operasinya dibatasi sesuai dengan peran yang dimiliki.Manajemen Tugas: Fungsionalitas CRUD untuk entitas tugas, dilengkapi dengan validasi data dan aturan bisnis yang spesifik.Middleware Kustom:CheckUserStatus: Middleware untuk memvalidasi dan memblokir permintaan dari pengguna dengan status nonaktif.LogRequest: Middleware untuk mencatat setiap permintaan API yang masuk ke dalam basis data sebagai log aktivitas.Penjadwal Tugas (Scheduler): Pemanfaatan Laravel Scheduler untuk mengeksekusi perintah Artisan secara periodik, yang berfungsi untuk memeriksa dan mencatat tugas yang telah melewati batas waktu.Pengujian Sistem Manajemen Tugas dan Pengguna
Pendahuluan
Dokumen ini menyajikan deskripsi teknis mengenai implementasi sebuah sistem RESTful API yang dirancang untuk manajemen tugas dan pengguna. Sistem ini dilengkapi dengan antarmuka dasbor frontend yang fungsional. Aplikasi ini dikembangkan sebagai pemenuhan Tes Evaluasi Kemampuan Fullstack Developer, dengan penekanan utama pada penerapan Role-Based Access Control (RBAC), logika bisnis yang terdefinisi, serta arsitektur perangkat lunak yang terstruktur.

Platform Backend: Laravel 11

Platform Frontend: Vanilla JavaScript, HTML, Bootstrap 5

Basis Data: MySQL

Mekanisme Otentikasi: Laravel Sanctum

Fungsionalitas Sistem
Fungsionalitas Backend
Otentikasi API: Implementasi mekanisme otentikasi yang aman untuk API menggunakan paket Laravel Sanctum.

Kontrol Akses Berbasis Peran (RBAC): Sistem mengimplementasikan tiga peran pengguna (admin, manager, staff), masing-masing dengan hak akses yang tergranulasi untuk setiap endpoint.

Manajemen Pengguna: Fungsionalitas CRUD (Create, Read, Update, Delete) untuk entitas pengguna, yang operasinya dibatasi sesuai dengan peran yang dimiliki.

Manajemen Tugas: Fungsionalitas CRUD untuk entitas tugas, dilengkapi dengan validasi data dan aturan bisnis yang spesifik.

Middleware Kustom:

CheckUserStatus: Middleware untuk memvalidasi dan memblokir permintaan dari pengguna dengan status nonaktif.

LogRequest: Middleware untuk mencatat setiap permintaan API yang masuk ke dalam basis data sebagai log aktivitas.

Penjadwal Tugas (Scheduler): Pemanfaatan Laravel Scheduler untuk mengeksekusi perintah Artisan secara periodik, yang berfungsi untuk memeriksa dan mencatat tugas yang telah melewati batas waktu.

Pengujian Perangkat Lunak:

Feature Tests: Pengujian yang mencakup verifikasi fungsionalitas endpoint API, proses otentikasi, dan mekanisme otorisasi.

Unit Tests: Pengujian terisolasi untuk unit-unit logika bisnis yang kritikal.

Fungsionalitas Frontend
Dasbor Responsif: Antarmuka pengguna dasbor yang dirancang secara responsif dengan navigasi bilah sisi (sidebar).

Antarmuka Adaptif Berbasis Peran: Komponen antarmuka pengguna, seperti menu navigasi, tombol aksi, dan data yang ditampilkan, disesuaikan secara dinamis berdasarkan peran pengguna yang sedang aktif.

Fungsionalitas Manajemen Tugas: Kemampuan untuk membuat, menampilkan, menyunting (melalui jendela modal), dan menghapus data tugas langsung dari antarmuka pengguna.

Fungsionalitas Manajemen Pengguna: Kemampuan untuk menampilkan daftar pengguna, dengan hak pembuatan pengguna baru yang terbatas hanya untuk peran admin.

Validasi Form Klien: Penerapan validasi input pada form di sisi klien menggunakan komponen dan kelas dari Bootstrap.

Interaksi Asinkron: Seluruh komunikasi data antara frontend dan backend diimplementasikan secara asinkron menggunakan fetch() API untuk mencegah pemuatan ulang halaman.

Diagram Relasi Entitas (ERD)
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

Dokumentasi Antarmuka Aplikasi
Berikut adalah beberapa tangkapan layar dari antarmuka aplikasi yang sedang beroperasi.

1. Halaman Login
Antarmuka halaman login untuk proses otentikasi pengguna.

2. Dasbor dan Manajemen Tugas (CRUD)
Antarmuka utama yang menampilkan daftar tugas dalam format kartu. Tombol aksi (Edit & Delete) ditampilkan secara kondisional berdasarkan hak akses pengguna.

3. Modal Penyuntingan Tugas
Formulir modal yang digunakan untuk menyunting detail dari sebuah tugas.

4. Hasil Eksekusi Tes
Keluaran dari eksekusi php artisan test --coverage pada terminal, yang menunjukkan hasil pengujian dan persentase cakupan kode.

Spesifikasi API Endpoint
Metode

URI

Deskripsi

Otorisasi Peran

POST

/api/login

Melakukan otentikasi pengguna dan menghasilkan token akses.

Publik

GET

/api/users

Mengambil daftar pengguna.

admin, manager

POST

/api/users

Membuat entitas pengguna baru.

admin

GET

/api/tasks

Mengambil daftar tugas yang relevan dengan peran pengguna.

admin, manager, staff

POST

/api/tasks

Membuat entitas tugas baru.

admin, manager

PUT

/api/tasks/{id}

Memperbarui entitas tugas yang ada.

admin, manager, staff

DELETE

/api/tasks/{id}

Menghapus entitas tugas.

admin, manager, staff

GET

/api/logs

Mengambil daftar log aktivitas sistem.

admin

Prosedur Instalasi dan Konfigurasi
Prosedur berikut menguraikan langkah-langkah yang diperlukan untuk mengkonfigurasi dan menjalankan proyek ini pada lingkungan pengembangan lokal.

Kloning Repositori

git clone [URL_REPOSITORY_ANDA]
cd [NAMA_FOLDER_PROYEK]

Instalasi Dependensi

composer install

Konfigurasi File Lingkungan

cp .env.example .env
php artisan key:generate

Konfigurasi Basis Data
Buka file .env dan sesuaikan parameter koneksi basis data Anda.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_evaluasi_fullstack
DB_USERNAME=root
DB_PASSWORD=

Migrasi dan Seeding Basis Data
Perintah ini akan membangun skema basis data dan mengisinya dengan data awal.

php artisan migrate:fresh --seed

Prosedur Operasional
Inisiasi Server Aplikasi

php artisan serve

Akses Antarmuka Pengguna
Buka peramban web dan navigasikan ke alamat http://127.0.0.1:8000.

Eksekusi Pengujian
Eksekusi rangkaian pengujian (Feature dan Unit) beserta laporan cakupan kode dapat dilakukan dengan menggunakan perintah berikut:

php artisan test --coverage

Kredensial Akses Standar
Kredensial standar berikut, yang dihasilkan oleh proses seeding, dapat digunakan untuk mengakses sistem:

Peran Admin: admin@example.com / password

Peran Manager: manager@example.com / password

Peran Staff: staff@example.com / password

Contoh Konfigurasi .env.example
Konten file .env.example yang esensial untuk konfigurasi lingkungan proyek adalah sebagai berikut:

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_evaluasi_fullstack
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
Perangkat Lunak:Feature Tests: Pengujian yang mencakup verifikasi fungsionalitas endpoint API, proses otentikasi, dan mekanisme otorisasi.Unit Tests: Pengujian terisolasi untuk unit-unit logika bisnis yang kritikal.Fungsionalitas FrontendDasbor Responsif: Antarmuka pengguna dasbor yang dirancang secara responsif dengan navigasi bilah sisi (sidebar).Antarmuka Adaptif Berbasis Peran: Komponen antarmuka pengguna, seperti menu navigasi, tombol aksi, dan data yang ditampilkan, disesuaikan secara dinamis berdasarkan peran pengguna yang sedang aktif.Fungsionalitas Manajemen Tugas: Kemampuan untuk membuat, menampilkan, menyunting (melalui jendela modal), dan menghapus data tugas langsung dari antarmuka pengguna.Fungsionalitas Manajemen Pengguna: Kemampuan untuk menampilkan daftar pengguna, dengan hak pembuatan pengguna baru yang terbatas hanya untuk peran admin.Validasi Form Klien: Penerapan validasi input pada form di sisi klien menggunakan komponen dan kelas dari Bootstrap.Interaksi Asinkron: Seluruh komunikasi data antara frontend dan backend diimplementasikan secara asinkron menggunakan fetch() API untuk mencegah pemuatan ulang halaman.Diagram Relasi Entitas (ERD)erDiagram
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
Dokumentasi Antarmuka AplikasiBerikut adalah beberapa tangkapan layar dari antarmuka aplikasi yang sedang beroperasi.1. Halaman LoginAntarmuka halaman login untuk proses otentikasi pengguna.2. Dasbor dan Manajemen Tugas (CRUD)Antarmuka utama yang menampilkan daftar tugas dalam format kartu. Tombol aksi (Edit & Delete) ditampilkan secara kondisional berdasarkan hak akses pengguna.3. Modal Penyuntingan TugasFormulir modal yang digunakan untuk menyunting detail dari sebuah tugas.4. Hasil Eksekusi TesKeluaran dari eksekusi php artisan test --coverage pada terminal, yang menunjukkan hasil pengujian dan persentase cakupan kode.Spesifikasi API EndpointMetodeURIDeskripsiOtorisasi PeranPOST/api/loginMelakukan otentikasi pengguna dan menghasilkan token akses.PublikGET/api/usersMengambil daftar pengguna.admin, managerPOST/api/usersMembuat entitas pengguna baru.adminGET/api/tasksMengambil daftar tugas yang relevan dengan peran pengguna.admin, manager, staffPOST/api/tasksMembuat entitas tugas baru.admin, managerPUT/api/tasks/{id}Memperbarui entitas tugas yang ada.admin, manager, staffDELETE/api/tasks/{id}Menghapus entitas tugas.admin, manager, staffGET/api/logsMengambil daftar log aktivitas sistem.adminProsedur Instalasi dan KonfigurasiProsedur berikut menguraikan langkah-langkah yang diperlukan untuk mengkonfigurasi dan menjalankan proyek ini pada lingkungan pengembangan lokal.Kloning Repositorigit clone [URL_REPOSITORY_ANDA]
cd [NAMA_FOLDER_PROYEK]
Instalasi Dependensicomposer install
Konfigurasi File Lingkungancp .env.example .env
php artisan key:generate
Konfigurasi Basis DataBuka file .env dan sesuaikan parameter koneksi basis data Anda.DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_evaluasi_fullstack
DB_USERNAME=root
DB_PASSWORD=
Migrasi dan Seeding Basis DataPerintah ini akan membangun skema basis data dan mengisinya dengan data awal.php artisan migrate:fresh --seed
Prosedur OperasionalInisiasi Server Aplikasiphp artisan serve
Akses Antarmuka PenggunaBuka peramban web dan navigasikan ke alamat http://127.0.0.1:8000.Eksekusi PengujianEksekusi rangkaian pengujian (Feature dan Unit) beserta laporan cakupan kode dapat dilakukan dengan menggunakan perintah berikut:php artisan test --coverage
Kredensial Akses StandarKredensial standar berikut, yang dihasilkan oleh proses seeding, dapat digunakan untuk mengakses sistem:Peran Admin: admin@example.com / passwordPeran Manager: manager@example.com / passwordPeran Staff: staff@example.com / passwordContoh Konfigurasi .env.exampleKonten file .env.example yang esensial untuk konfigurasi lingkungan proyek adalah sebagai berikut:APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_evaluasi_fullstack
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
