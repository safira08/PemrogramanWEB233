# FlipPlay REST API — Modul 4 (Laravel)

Proyek ini adalah implementasi **REST API FlipPlay** menggunakan **Laravel**, melanjutkan simulasi OOP dari Modul 3. Proyek ini menghubungkan logika bisnis ke database SQLite/MySQL dan mengeksposnya sebagai endpoint RESTful API lengkap dengan **Eloquent Relationship (Many-to-Many)**, **Migrations**, **Controllers**, **Form Request Validation**, dan **API Resource Response**.

---

## 📌 Ketentuan & Fitur yang Diterapkan

1. **Entitas & Relasi Eloquent**:
   - `Media`: Representasi media (judul, jenis/tipe, genre, tahun rilis, durasi, deskripsi).
   - `User`: Pengguna sistem FlipPlay.
   - `Collection` (Pivot Table `collections`): Relasi `belongsToMany` antara `User` dan `Media`, dilengkapi `notes` dan timestamp `added_at`. Mencegah penambahan media yang sama dua kali via unique constraint.
2. **Migrations & Foreign Keys**:
   - `create_media_table`
   - `create_collections_table` dengan foreign key cascade ke `users` dan `media`.
3. **CRUD Standar Media**:
   - `GET /api/media` (dengan fitur query search filter berdasarkan judul/genre).
   - `POST /api/media` (tambah media baru dengan validasi).
   - `GET /api/media/{id}` (detail media beserta daftar pengguna yang mengoleksinya).
   - `PUT /api/media/{id}` (update data media).
   - `DELETE /api/media/{id}` (hapus media).
4. **Form Request Validation**:
   - `StoreMediaRequest`: Judul wajib diisi, unik (`unique:media,title`), tipe valid, dll.
   - `UpdateMediaRequest`: Validasi update dengan mengabaikan ID media yang sedang diedit.
   - `AddMediaToCollectionRequest`: ID media wajib ada di database.
5. **API Resource**:
   - `MediaResource`: Format JSON konsisten, menyertakan relasi `whenLoaded('users')` dan detail pivot.
   - `UserResource`: Format JSON konsisten untuk profil dan `whenLoaded('media')`.
6. **Custom Action Relasi Antartabel**:
   - `GET /api/users/{user}/collections` : Menampilkan koleksi media milik pengguna tertentu.
   - `POST /api/users/{user}/collections` : Menambahkan media ke koleksi pribadi pengguna (mencegah duplikasi).
   - `DELETE /api/users/{user}/collections/{media}` : Menghapus media dari koleksi pribadi pengguna.

---

## 🚀 Panduan Menjalankan Project

### 1. Menyiapkan Dependensi & Database
Jalankan perintah berikut di terminal:
```bash
# Salin konfigurasi environment jika belum ada
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi dan isi database dengan contoh data awal (Seeder)
php artisan migrate:fresh --seed
```

### 2. Menjalankan Server Laravel
```bash
php artisan serve
```
Server akan aktif di: `http://127.0.0.1:8000` (Base API: `http://127.0.0.1:8000/api`)

---

## 📋 Daftar Endpoint API

Verifikasi endpoint dapat dicek dengan:
```bash
php artisan route:list
```

| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/media` | Menampilkan seluruh media (support `?search=keyword` atau `?title=xxx`) |
| `POST` | `/api/media` | Menambahkan media baru |
| `GET` | `/api/media/{media}` | Menampilkan detail media + pengguna yang mengoleksinya |
| `PUT/PATCH` | `/api/media/{media}` | Memperbarui data media |
| `DELETE` | `/api/media/{media}` | Menghapus media dari sistem |
| `GET` | `/api/users/{user}/collections` | Menampilkan daftar koleksi media milik pengguna |
| `POST` | `/api/users/{user}/collections` | Menambahkan media ke koleksi pribadi pengguna |
| `DELETE` | `/api/users/{user}/collections/{media}` | Menghapus media dari koleksi pengguna |
| `GET` | `/api/users` | Menampilkan daftar seluruh pengguna |
| `POST` | `/api/users` | Menambahkan pengguna baru |

---

## 🧪 Pengujian Menggunakan Postman

File koleksi Postman yang siap di-import sudah disediakan pada root project:
📁 `FlipPlay_API_Postman_Collection.json`

### Cara Import ke Postman:
1. Buka aplikasi **Postman**.
2. Klik tombol **Import** di kiri atas.
3. Pilih file `FlipPlay_API_Postman_Collection.json` dari folder project ini.
4. Koleksi **FlipPlay REST API - Modul 4** akan langsung muncul lengkap dengan seluruh request.

### Skenario Pengujian yang Tersedia:
- **Happy Path**:
  - GET all media, Search media by title
  - POST new media (201 Created)
  - GET media detail with users (200 OK)
  - POST add media to collection (201 Created)
  - GET user collections (200 OK)
  - DELETE remove media from collection (200 OK)
  - DELETE media from system (200 OK)
- **Skenario Gagal (Negative Testing)**:
  - POST media dengan judul kosong (`422 Unprocessable Content`)
  - POST media dengan judul duplikat yang sudah ada (`422 Unprocessable Content`)
  - POST add media yang sudah ada di koleksi pengguna (`422 Unprocessable Content`)
  - DELETE media dari koleksi yang tidak ada (`404 Not Found`)

---

## 🧪 Menjalankan Automated Test (PHPUnit)

Seluruh 14 skenario pengujian unit & feature test dapat dijalankan otomatis:
```bash
php artisan test --testdox
```

---

## 🌐 Menghubungkan ke GitHub Repository

Untuk mengunggah project ini ke repositori GitHub Anda:

1. Buat repositori baru di akun GitHub Anda (misal: `flipplay-rest-api`).
2. Di terminal, hubungkan remote dan push:
```bash
git branch -M main
git remote add origin https://github.com/<username-github-anda>/flipplay-rest-api.git
git push -u origin main
```
