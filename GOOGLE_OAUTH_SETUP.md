# 🔐 Google OAuth Setup Guide

Panduan lengkap untuk setup Google OAuth Login di Sistem Antrian Service.

---

## 📋 Prerequisites

- Laravel Socialite sudah terinstall ✅
- Google Cloud Console account
- Database sudah running dan migrasi sudah dijalankan

---

## 🚀 Setup Google Cloud Console

### 1. Buat Project Baru

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Klik **Select a project** → **New Project**
3. Nama project: `Sistem Antrian Service`
4. Klik **Create**

### 2. Enable Google+ API

1. Di sidebar, pilih **APIs & Services** → **Library**
2. Cari "Google+ API"
3. Klik **Enable**

### 3. Create OAuth 2.0 Credentials

1. Di sidebar, pilih **APIs & Services** → **Credentials**
2. Klik **Create Credentials** → **OAuth client ID**
3. Jika diminta, configure OAuth consent screen:
   - User Type: **External**
   - App name: `Sistem Antrian Service`
   - User support email: (email Anda)
   - Developer contact: (email Anda)
   - Klik **Save and Continue**
   - Scopes: Skip (klik **Save and Continue**)
   - Test users: Tambahkan email untuk testing
   - Klik **Save and Continue**

4. Kembali ke **Create OAuth client ID**:
   - Application type: **Web application**
   - Name: `Sistem Antrian Web Client`
   - Authorized redirect URIs:
     - `http://localhost:8000/api/auth/google/callback` (untuk development)
     - `https://yourdomain.com/api/auth/google/callback` (untuk production)
   - Klik **Create**

5. **Copy** Client ID dan Client Secret yang muncul

---

## ⚙️ Konfigurasi Laravel

### 1. Update File `.env`

Tambahkan konfigurasi Google OAuth di file `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT=http://localhost:8000/api/auth/google/callback
```

**Note:** Ganti `your-client-id` dan `your-client-secret` dengan credentials dari Google Cloud Console.

### 2. Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📡 API Endpoints

### 1. **Redirect to Google Login**
**Endpoint:** `GET /api/auth/google`

**Deskripsi:** Redirect user ke halaman login Google

**Response:** Redirect ke Google OAuth consent screen

**Cara Akses:**
- Buka browser: `http://localhost:8000/api/auth/google`
- Atau dari frontend: `window.location.href = 'http://localhost:8000/api/auth/google'`

---

### 2. **Google OAuth Callback**
**Endpoint:** `GET /api/auth/google/callback`

**Deskripsi:** Endpoint yang dipanggil Google setelah user login

**Response (Success):**
```json
{
  "message": "Login berhasil",
  "user": {
    "user_id": "01JBEXAMPLE789012",
    "google_id": "1234567890",
    "full_name": "John Doe",
    "email": "john@gmail.com",
    "role": "petugas",
    "counter_id": null,
    "email_verified_at": "2025-10-30T04:00:00.000000Z",
    "created_at": "2025-10-30T04:00:00.000000Z",
    "updated_at": "2025-10-30T04:00:00.000000Z"
  },
  "access_token": "1|abcdefghijklmnopqrstuvwxyz1234567890",
  "token_type": "Bearer"
}
```

**Response (Error):**
```json
{
  "message": "Login gagal",
  "error": "Error message here"
}
```

---

### 3. **Get Current User**
**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer {access_token}
Accept: application/json
```

**Response:**
```json
{
  "user_id": "01JBEXAMPLE789012",
  "google_id": "1234567890",
  "full_name": "John Doe",
  "email": "john@gmail.com",
  "role": "petugas",
  "counter_id": null,
  "email_verified_at": "2025-10-30T04:00:00.000000Z",
  "created_at": "2025-10-30T04:00:00.000000Z",
  "updated_at": "2025-10-30T04:00:00.000000Z"
}
```

---

### 4. **Logout**
**Endpoint:** `POST /api/auth/logout`

**Headers:**
```
Authorization: Bearer {access_token}
Accept: application/json
```

**Response:**
```json
{
  "message": "Logout berhasil"
}
```

---

## 🔄 Login Flow

### Flow Diagram:
```
1. User klik "Login with Google"
   ↓
2. Frontend redirect ke: GET /api/auth/google
   ↓
3. Laravel redirect ke Google OAuth
   ↓
4. User login di Google & approve permissions
   ↓
5. Google redirect ke: GET /api/auth/google/callback
   ↓
6. Laravel:
   - Cek apakah user sudah ada (by email)
   - Jika ada: update google_id & verify email
   - Jika belum: create user baru dengan role "petugas"
   - Generate Sanctum access token
   ↓
7. Return JSON response dengan user data & access_token
   ↓
8. Frontend simpan access_token (localStorage/cookie)
   ↓
9. Frontend gunakan token untuk request selanjutnya
```

### Behavior:
- **User Baru:** Otomatis dibuat dengan role `petugas`, `counter_id` = null
- **User Existing:** Update `google_id` jika belum ada
- **Email Verification:** Otomatis terverifikasi karena dari Google
- **Token:** Menggunakan Laravel Sanctum untuk authentication

---

## 💻 Contoh Implementasi Frontend

### Vanilla JavaScript

```html
<!DOCTYPE html>
<html>
<head>
    <title>Login with Google</title>
</head>
<body>
    <button onclick="loginWithGoogle()">Login with Google</button>
    <div id="user-info" style="display:none;">
        <h3>Welcome, <span id="user-name"></span>!</h3>
        <button onclick="logout()">Logout</button>
    </div>

    <script>
        const API_URL = 'http://localhost:8000/api';

        function loginWithGoogle() {
            // Redirect to Google OAuth
            window.location.href = `${API_URL}/auth/google`;
        }

        // Handle callback (dipanggil setelah redirect dari Google)
        function handleCallback() {
            const urlParams = new URLSearchParams(window.location.search);
            // Dalam implementasi nyata, Anda perlu handle response dari callback
            // Biasanya menggunakan popup window atau redirect flow
        }

        function logout() {
            const token = localStorage.getItem('access_token');
            
            fetch(`${API_URL}/auth/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                localStorage.removeItem('access_token');
                location.reload();
            });
        }

        // Get current user
        function getCurrentUser() {
            const token = localStorage.getItem('access_token');
            
            if (token) {
                fetch(`${API_URL}/auth/me`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(user => {
                    document.getElementById('user-name').textContent = user.full_name;
                    document.getElementById('user-info').style.display = 'block';
                });
            }
        }

        // Check if user is logged in on page load
        getCurrentUser();
    </script>
</body>
</html>
```

### React Example

```jsx
import { useEffect, useState } from 'react';

function App() {
  const [user, setUser] = useState(null);
  const API_URL = 'http://localhost:8000/api';

  const loginWithGoogle = () => {
    window.location.href = `${API_URL}/auth/google`;
  };

  const logout = async () => {
    const token = localStorage.getItem('access_token');
    
    await fetch(`${API_URL}/auth/logout`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    
    localStorage.removeItem('access_token');
    setUser(null);
  };

  const getCurrentUser = async () => {
    const token = localStorage.getItem('access_token');
    
    if (token) {
      const response = await fetch(`${API_URL}/auth/me`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      
      const userData = await response.json();
      setUser(userData);
    }
  };

  useEffect(() => {
    getCurrentUser();
  }, []);

  return (
    <div>
      {user ? (
        <div>
          <h3>Welcome, {user.full_name}!</h3>
          <p>Email: {user.email}</p>
          <p>Role: {user.role}</p>
          <button onClick={logout}>Logout</button>
        </div>
      ) : (
        <button onClick={loginWithGoogle}>Login with Google</button>
      )}
    </div>
  );
}

export default App;
```

---

## 🧪 Testing

### 1. Test dengan Browser

1. Jalankan server: `php artisan serve`
2. Buka browser: `http://localhost:8000/api/auth/google`
3. Login dengan Google account
4. Setelah berhasil, Anda akan mendapat JSON response dengan `access_token`
5. Copy `access_token` untuk testing endpoint lain

### 2. Test dengan Postman

**Get Current User:**
```
GET http://localhost:8000/api/auth/me
Headers:
  Authorization: Bearer {your_access_token}
  Accept: application/json
```

**Logout:**
```
POST http://localhost:8000/api/auth/logout
Headers:
  Authorization: Bearer {your_access_token}
  Accept: application/json
```

---

## 🔒 Security Notes

1. **Stateless OAuth:** Menggunakan `stateless()` untuk API-based authentication
2. **Token Storage:** Simpan access_token di localStorage atau httpOnly cookie
3. **HTTPS:** Untuk production, WAJIB menggunakan HTTPS
4. **Token Expiry:** Sanctum token tidak expire by default, Anda bisa set expiry di `config/sanctum.php`
5. **CORS:** Pastikan CORS sudah dikonfigurasi jika frontend di domain berbeda

---

## 🐛 Troubleshooting

### Error: "redirect_uri_mismatch"
- Pastikan redirect URI di Google Console sama persis dengan yang di `.env`
- Cek tidak ada trailing slash
- Cek protocol (http vs https)

### Error: "Invalid credentials"
- Cek `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` di `.env`
- Jalankan `php artisan config:clear`

### Error: "Unauthenticated"
- Pastikan token dikirim di header `Authorization: Bearer {token}`
- Cek token masih valid (belum di-logout)

### User tidak ter-create
- Cek database connection
- Cek migrasi sudah dijalankan
- Cek log di `storage/logs/laravel.log`

---

## 📚 Additional Resources

- [Laravel Socialite Documentation](https://laravel.com/docs/11.x/socialite)
- [Laravel Sanctum Documentation](https://laravel.com/docs/11.x/sanctum)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)

---

## ✅ Checklist

- [ ] Google Cloud Project created
- [ ] OAuth credentials created
- [ ] `.env` configured with Google credentials
- [ ] Config cache cleared
- [ ] Test login flow in browser
- [ ] Frontend implementation
- [ ] Production redirect URI added to Google Console

---

**Happy Coding! 🚀**
