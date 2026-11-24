# FlipPlay REST API — Modul 4 (Laravel)

Proyek ini adalah implementasi **REST API FlipPlay** menggunakan **Laravel**, melanjutkan simulasi OOP dari Modul 3. Proyek ini menghubungkan logika bisnis ke database MySQL (melalui Laragon) dan mengeksposnya sebagai endpoint RESTful API lengkap dengan **Eloquent Relationship (Many-to-Many)**, **Migrations**, **Controllers**, **Form Request Validation**, dan **API Resource Response**.

Sistem mengelola 4 jenis media resmi FlipPlay:
1. **anime**
2. **drama**
3. **manga**
4. **light novel**

---

## 📌 Ketentuan & Fitur yang Diterapkan

1. **Entitas & Relasi Eloquent**:
   - `Media`: Representasi media (judul, jenis: `anime|drama|manga|light novel`, genre, tahun rilis, durasi, deskripsi).
   - `User`: Pengguna sistem FlipPlay.
   - `Collection` (Pivot Table `media_user`): Relasi `belongsToMany` antara `User` dan `Media` dengan kolom `notes` dan timestamp `added_at`. Mencegah penambahan media yang sama dua kali via compound unique key `(user_id, media_id)`.
2. **Migrations & Foreign Keys**:
   - `create_media_table`
   - `create_media_user_table` dengan foreign key cascade ke `users` dan `media`.
3. **CRUD Standar Media**:
   - `GET /api/media` (dengan fitur query search filter berdasarkan judul/genre/tipe).
   - `POST /api/media` (tambah media baru dengan validasi).
   - `GET /api/media/{id}` (detail media beserta daftar pengguna yang mengoleksinya).
   - `PUT /api/media/{id}` (update data media).
   - `DELETE /api/media/{id}` (hapus media).
4. **Form Request Validation**:
   - `StoreMediaRequest`: Judul wajib diisi, unik (`unique:media,title`), tipe harus `anime|drama|manga|light novel`, dll.
   - `UpdateMediaRequest`: Validasi update dengan mengecualikan ID media saat ini.
   - `AddMediaToCollectionRequest`: ID media wajib ada di database.
5. **API Resource**:
   - `MediaResource`: Format JSON konsisten, menyertakan relasi `whenLoaded('users')` dan detail pivot.
   - `UserResource`: Format JSON konsisten untuk profil dan `whenLoaded('media')`.
6. **Custom Action Relasi Antartabel**:
   - `GET /api/users/{user}/collections` : Menampilkan koleksi media milik pengguna tertentu.
   - `POST /api/users/{user}/collections` : Menambahkan media ke koleksi pribadi pengguna (mencegah duplikasi dengan status HTTP 409 Conflict).
   - `DELETE /api/users/{user}/collections/{media}` : Menghapus media dari koleksi pribadi pengguna.

---

## 🚀 Panduan Menjalankan Project

### 1. Menyiapkan Database di Laragon (MySQL)
1. Buka **Laragon** dan klik **Start All**.
2. Buat database baru bernama **`flipplay_db`** melalui HeidiSQL / phpMyAdmin.
3. Jalankan migrasi dan isi database dengan contoh data awal resmi FlipPlay (Seeder):
   ```bash
   php artisan migrate:fresh --seed
   ```

### 2. Menjalankan Server Laravel
```bash
php artisan serve
```
Server akan aktif di: `http://127.0.0.1:8000` (Base API: `http://127.0.0.1:8000/api`)

---

## 📋 Data Awal FlipPlay (Seeder)

| ID | Judul | Jenis (`type`) | Genre | Rilis | Durasi |
| :---: | :--- | :--- | :--- | :---: | :---: |
| 1 | **Attack on Titan** | `anime` | Action / Dark Fantasy | 2013 | 24 |
| 2 | **Moving** | `drama` | Sci-Fi / Action | 2023 | 50 |
| 3 | **One Piece** | `manga` | Adventure / Fantasy | 1997 | 1100 |
| 4 | **Solo Leveling** | `light novel` | Action / Fantasy | 2016 | 270 |

---

## 🧪 Pengujian Menggunakan Postman

File koleksi Postman yang siap di-import:
📁 `FlipPlay_API_Postman_Collection.json`

### Skenario Pengujian:
- **Happy Path**:
  - `GET /api/media` (200 OK)
  - `GET /api/media?title=Attack` (200 OK - Filter Index)
  - `GET /api/media/1` (200 OK - Detail Attack on Titan + kolektor)
  - `POST /api/media` (201 Created - Tambah Jujutsu Kaisen)
  - `PUT /api/media/1` (200 OK - Update data media)
  - `DELETE /api/media/4` (200 OK - Hapus Solo Leveling)
  - `GET /api/users/1/collections` (200 OK - Lihat koleksi Ahmad)
  - `POST /api/users/1/collections` (201 Created - Tambah Moving ke koleksi Ahmad)
  - `DELETE /api/users/1/collections/3` (200 OK - Hapus One Piece dari koleksi Ahmad)
- **Skenario Gagal (Failure Path)**:
  - `POST /api/media` judul kosong (`422 Unprocessable Content`)
  - `POST /api/media` judul duplikat Attack on Titan (`422 Unprocessable Content`)
  - `POST /api/users/1/collections` media duplikat Attack on Titan (`409 Conflict`)
  - `DELETE /api/users/1/collections/99` media tidak ada di koleksi (`404 Not Found`)

---

## 🧪 Menjalankan Automated Test (PHPUnit)

```bash
php artisan test --testdox
```
*(Seluruh 14 test cases passed 100%)*
