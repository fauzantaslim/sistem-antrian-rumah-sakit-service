# 📮 Postman Collection Guide - Sistem Antrian Service API

Panduan lengkap untuk menggunakan Postman Collection API Sistem Antrian Service dengan Google OAuth Login, Email Verification, dan User Management.

## 📁 File yang Tersedia

1. **sistem-antrian.postman_collection.json** - Collection dengan semua endpoint API (Updated)
2. **sistem-antrian.postman_environment.json** - Environment variables untuk local development

---

## 🚀 Cara Import ke Postman

### 1. Import Collection

1. Buka Postman
2. Klik **Import** (tombol di kiri atas)
3. Pilih tab **File**
4. Drag & drop atau browse file `sistem-antrian.postman_collection.json`
5. Klik **Import**

### 2. Import Environment

1. Klik icon ⚙️ (Settings) di kanan atas
2. Pilih **Environments**
3. Klik **Import**
4. Pilih file `sistem-antrian.postman_environment.json`
5. Klik **Import**

### 3. Aktifkan Environment

1. Di dropdown environment (kanan atas), pilih **Sistem Antrian - Local**
2. Pastikan `base_url` sudah ter-set ke `http://localhost:8000`

---

## 📋 Struktur Collection

Collection ini terbagi menjadi 3 folder utama:

### 1️⃣ Authentication (Google OAuth)
Endpoint untuk autentikasi:
- **GET** `/api/auth/google` - Login with Google (Redirect)
- **GET** `/api/auth/me` - Get Current User (Protected)
- **POST** `/api/auth/logout` - Logout (Protected)

### 2️⃣ User Management (Protected)
Endpoint untuk CRUD operations user:
- **GET** `/api/users` - Get All Users (with pagination, search, sorting)
- **POST** `/api/users` - Create User
- **GET** `/api/users/{user_id}` - Get Single User
- **PUT** `/api/users/{user_id}` - Update User (full)
- **PATCH** `/api/users/{user_id}` - Update User (partial)
- **DELETE** `/api/users/{user_id}` - Delete User

### 3️⃣ Email Verification
Endpoint untuk verifikasi email:
- **GET** `/api/users/{user_id}/verify` - Verify Email
- **POST** `/api/users/{user_id}/resend-verification` - Resend Verification Email

---

## 🧪 Testing Flow

### Skenario 1: Create & Verify User

1. **Create User**
   - Request: `POST /api/users`
   - Body:
     ```json
     {
       "full_name": "John Doe",
       "email": "john@example.com",
       "role": "admin",
       "counter_id": "01JBEXAMPLE123456"
     }
     ```
   - Response akan otomatis menyimpan `user_id` ke environment variable
   - Email verifikasi akan dikirim (cek log jika menggunakan `MAIL_MAILER=log`)

2. **Get All Users**
   - Request: `GET /api/users`
   - Lihat semua user yang terdaftar

3. **Verify Email**
   - Request: `GET /api/users/{{user_id}}/verify`
   - Variable `{{user_id}}` akan otomatis terisi dari step 1

4. **Get Single User**
   - Request: `GET /api/users/{{user_id}}`
   - Cek field `email_verified_at` sudah terisi

### Skenario 2: Update User

1. **Update Full Data**
   - Request: `PUT /api/users/{{user_id}}`
   - Body: Semua field wajib diisi

2. **Update Partial Data**
   - Request: `PATCH /api/users/{{user_id}}`
   - Body: Hanya field yang ingin diubah
   - Contoh:
     ```json
     {
       "full_name": "John Doe Updated"
     }
     ```

### Skenario 3: Resend Verification

1. **Create User** (jika belum ada)
2. **Resend Verification Email**
   - Request: `POST /api/users/{{user_id}}/resend-verification`
   - Email verifikasi akan dikirim ulang

---

## 🔧 Environment Variables

Collection ini menggunakan environment variables berikut:

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost:8000` | Base URL API server |
| `access_token` | (manual) | Bearer token dari Google OAuth login |
| `user_id` | (auto-filled) | User ID dari response Create User |
| `user_email` | (auto-filled) | Email dari response Create User |
| `counter_id` | `01JBEXAMPLE123456` | Counter ID untuk testing |

**Note:** 
- `user_id` dan `user_email` akan otomatis terisi saat Anda create user baru
- `access_token` harus diisi manual setelah login via Google OAuth

---

## ✅ Validation Rules

### Create User (Required)
- `full_name`: string, max 255 karakter
- `email`: valid email, unique
- `role`: enum (admin, petugas)
- `counter_id`: string, harus exist di tabel counters

### Update User (Optional/Sometimes)
Semua field bersifat optional. Hanya field yang dikirim yang akan divalidasi dan diupdate.

---

## 📧 Email Verification Flow

1. **User dibuat** → Email verifikasi otomatis dikirim
2. **User klik link** di email → Redirect ke endpoint `/api/users/{id}/verify`
3. **Email terverifikasi** → Field `email_verified_at` terisi dengan timestamp
4. **User bisa login** → Hanya user dengan email terverifikasi yang bisa login

---

## 🐛 Troubleshooting

### Error: Connection Refused
- Pastikan Laravel server sudah running: `php artisan serve`
- Cek `base_url` di environment sesuai dengan server Anda

### Error: 404 Not Found
- Pastikan route sudah terdaftar: `php artisan route:list`
- Cek apakah endpoint URL sudah benar

### Error: 422 Validation Error
- Cek response body untuk detail error
- Pastikan semua required field sudah diisi dengan format yang benar
- Pesan error sudah dalam bahasa Indonesia

### Error: 500 Internal Server Error
- Cek `storage/logs/laravel.log` untuk detail error
- Pastikan database sudah running dan migrasi sudah dijalankan

---

## 📝 Response Examples

### Success Response (Create User)
```json
{
  "message": "User berhasil dibuat. Email verifikasi telah dikirim.",
  "user": {
    "user_id": "01JBEXAMPLE789012",
    "full_name": "John Doe",
    "email": "john@example.com",
    "role": "admin",
    "counter_id": "01JBEXAMPLE123456",
    "email_verified_at": null,
    "created_at": "2025-10-30T01:00:00.000000Z",
    "updated_at": "2025-10-30T01:00:00.000000Z"
  }
}
```

### Error Response (Validation)
```json
{
  "message": "Nama lengkap wajib diisi. (and 2 more errors)",
  "errors": {
    "full_name": [
      "Nama lengkap wajib diisi."
    ],
    "email": [
      "Email wajib diisi."
    ],
    "role": [
      "Role wajib diisi."
    ]
  }
}
```

---

## 🔐 Authentication Flow

### Login dengan Google OAuth

1. **Buka di Browser:**
   ```
   http://localhost:8000/api/auth/google
   ```
   
2. **Login dengan Google Account** yang sudah terdaftar di sistem

3. **Copy Access Token** dari response JSON:
   ```json
   {
     "status_code": 200,
     "success": true,
     "message": "Login berhasil",
     "data": {
       "user": {...},
       "access_token": "1|abcdefghijklmnopqrstuvwxyz...",
       "token_type": "Bearer"
     }
   }
   ```

4. **Set di Postman Environment:**
   - Klik icon 👁️ (eye) di kanan atas
   - Edit environment **Sistem Antrian - Local**
   - Paste token ke variable `access_token`
   - Save

5. **Test Protected Endpoint:**
   - Request: `GET /api/auth/me`
   - Akan return data user yang sedang login

### Protected Endpoints

Semua endpoint berikut memerlukan Bearer Token:
- `GET /api/auth/me`
- `POST /api/auth/logout`
- `GET /api/users` (with pagination, search, sorting)
- `POST /api/users`
- `GET /api/users/{id}`
- `PUT /api/users/{id}`
- `PATCH /api/users/{id}`
- `DELETE /api/users/{id}`

### Response Structure

Semua endpoint menggunakan struktur response yang konsisten:

**Success Response:**
```json
{
  "status_code": 200,
  "success": true,
  "message": "Pesan deskriptif",
  "data": {
    // Data object atau array
  }
}
```

**Error Response:**
```json
{
  "status_code": 403,
  "success": false,
  "message": "Pesan error",
  "data": {
    "error": "Detail error"
  }
}
```

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Postman Documentation](https://learning.postman.com/docs)
- [API Testing Best Practices](https://www.postman.com/api-platform/api-testing/)

---

## 🤝 Support

Jika ada pertanyaan atau issue, silakan hubungi tim development.

**Happy Testing! 🚀**
