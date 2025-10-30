# 🏥 Sistem Antrian Service - Backend API

RESTful API untuk sistem manajemen antrian berbasis Laravel dengan Google OAuth authentication, email verification, dan dashboard analytics.

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
  - [Google OAuth Setup](#1-google-oauth-setup)
  - [Gmail SMTP Setup](#2-gmail-smtp-setup)
- [Database Setup](#-database-setup)
- [API Documentation](#-api-documentation)
- [Testing with Postman](#-testing-with-postman)
- [Troubleshooting](#-troubleshooting)

---

## ✨ Features

### Authentication & Authorization
- ✅ Google OAuth 2.0 Login
- ✅ Laravel Sanctum Token-based Authentication
- ✅ Role-based Access Control (Admin, Petugas)
- ✅ Email Verification System

### User Management
- ✅ CRUD Operations for Users
- ✅ Pagination, Search, and Sorting
- ✅ Email Verification Workflow
- ✅ Counter Assignment

### Counter Management
- ✅ CRUD Operations for Counters
- ✅ Public Endpoint for Counter List
- ✅ Counter-User Relationship

### Queue Management
- ✅ Auto-generate Queue Numbers (based on counter creation order)
- ✅ Public Endpoint for Taking Queue Number
- ✅ Status Transition Validation (waiting → called → done)
- ✅ Display Monitor for Public View
- ✅ Daily Reset Queue Numbers

### Dashboard & Analytics
- ✅ Real-time Statistics (queues today, counters, users, avg wait time)
- ✅ Traffic Chart (daily, weekly, monthly, yearly, all-time)
- ✅ Queue per Counter Chart
- ✅ Status Distribution per Counter

---

## 🛠 Tech Stack

- **Framework:** Laravel 11.x
- **Database:** PostgreSQL
- **Authentication:** Laravel Sanctum + Google OAuth (Socialite)
- **Email:** Gmail SMTP / Mailtrap
- **API Documentation:** Postman Collection
- **ID Generation:** ULID

---

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- PostgreSQL
- Git

### Steps

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd sistem-antrian-service
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Copy Environment File**
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Configure Database** (see [Database Setup](#-database-setup))

6. **Run Migrations**
   ```bash
   php artisan migrate
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

   Server will run at `http://localhost:8000`

---

## ⚙️ Configuration

### 1. Google OAuth Setup

#### Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project: **Sistem Antrian Service**
3. Enable **Google+ API**
4. Create **OAuth 2.0 Credentials**:
   - Application type: **Web application**
   - Authorized redirect URIs:
     - `http://localhost:8000/api/auth/google/callback` (development)
     - `https://yourdomain.com/api/auth/google/callback` (production)

#### Update `.env`

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT=http://localhost:8000/api/auth/google/callback
```

#### Clear Config Cache

```bash
php artisan config:clear
php artisan cache:clear
```

---

### 2. Gmail SMTP Setup

#### Generate Gmail App Password

1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Enable **2-Step Verification** (Security → 2-Step Verification)
3. Generate **App Password**:
   - Security → App passwords
   - Select app: **Mail**
   - Select device: **Other (Custom name)**
   - Name: `Laravel Sistem Antrian`
   - Copy the 16-digit password

#### Update `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Antrian Service"
```

**Note:** Remove spaces from the 16-digit password.

#### Alternative: Mailtrap (Development)

For development, you can use [Mailtrap](https://mailtrap.io) instead:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sistem-antrian.com
MAIL_FROM_NAME="Sistem Antrian Service"
```

---

## 🗄 Database Setup

### PostgreSQL Configuration

Update `.env` with your PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sistem_antrian
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### Create Database

```sql
CREATE DATABASE sistem_antrian;
```

### Run Migrations

```bash
php artisan migrate
```

### Database Schema

**Tables:**
- `users` - User accounts with Google OAuth
- `counters` - Service counters/lokets
- `queues` - Queue records with status tracking

---

## 📚 API Documentation

### Base URL

```
http://localhost:8000/api
```

### API Structure

```
📁 Authentication (3 endpoints)
├── GET    /auth/google                    - Login with Google (Redirect)
├── GET    /auth/me                        - Get Current User 🔒
└── POST   /auth/logout                    - Logout 🔒

📁 User Management (6 endpoints) 🔒
├── GET    /users                          - Get All Users
├── POST   /users                          - Create User
├── GET    /users/{id}                     - Get Single User
├── PUT    /users/{id}                     - Update User (Full)
├── PATCH  /users/{id}                     - Update User (Partial)
└── DELETE /users/{id}                     - Delete User

📁 Counter Management (7 endpoints)
├── GET    /counters/list                  - Get Counter List (PUBLIC)
├── GET    /counters                       - Get All Counters 🔒
├── POST   /counters                       - Create Counter 🔒
├── GET    /counters/{id}                  - Get Single Counter 🔒
├── PUT    /counters/{id}                  - Update Counter 🔒
├── PATCH  /counters/{id}                  - Update Counter 🔒
└── DELETE /counters/{id}                  - Delete Counter 🔒

📁 Queue Management (5 endpoints)
├── POST   /queues                         - Create Queue (PUBLIC)
├── GET    /queues/display                 - Display Queue Monitor (PUBLIC)
├── GET    /queues                         - Get All Queues 🔒
├── GET    /queues/{id}                    - Get Single Queue 🔒
└── PATCH  /queues/{id}                    - Update Queue Status 🔒

📁 Dashboard (3 endpoints) 🔒
├── GET    /dashboard/stats                - Get Dashboard Statistics
├── GET    /dashboard/charts               - Get Dashboard Charts
└── GET    /dashboard/status-distribution  - Get Status Distribution

📁 Email Verification (2 endpoints)
├── GET    /users/{id}/verify              - Verify Email
└── POST   /users/{id}/resend-verification - Resend Verification Email
```

🔒 = Protected endpoint (requires Bearer token)

### Response Structure

All endpoints use consistent response structure:

**Success Response:**
```json
{
  "status_code": 200,
  "success": true,
  "message": "Descriptive message",
  "data": {
    // Response data
  }
}
```

**Error Response:**
```json
{
  "status_code": 400,
  "success": false,
  "message": "Error message",
  "data": {
    "error": "Error details"
  }
}
```

---

## 🧪 Testing with Postman

### Import Collection

1. Open Postman
2. Click **Import**
3. Select `sistem-antrian.postman_collection.json`
4. Import `sistem-antrian.postman_environment.json`
5. Select **Sistem Antrian - Local** environment

### Get Access Token

1. **Login via Browser:**
   ```
   http://localhost:8000/api/auth/google
   ```

2. **Copy Access Token** from JSON response

3. **Set in Postman:**
   - Click 👁️ (eye icon)
   - Edit environment
   - Paste token to `access_token` variable
   - Save

### Test Endpoints

All protected endpoints will automatically use the `access_token` from environment.

### Testing Flow Example

1. **Login** → Get access token
2. **Create Counter** → Get counter_id
3. **Create Queue** (PUBLIC) → Get queue_number
4. **Get Queue Display** (PUBLIC) → See all queues
5. **Update Queue Status** → Change status (waiting → called → done)
6. **View Dashboard Stats** → See statistics

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Google OAuth Error: "redirect_uri_mismatch"

**Solution:**
- Ensure redirect URI in Google Console matches exactly with `.env`
- Check for trailing slashes
- Verify protocol (http vs https)

#### 2. Email Not Sending

**Solution:**
- Check `MAIL_MAILER=smtp` (not `log`)
- Verify Gmail App Password (no spaces)
- Ensure 2-Step Verification is enabled
- Run `php artisan config:clear`
- Check `storage/logs/laravel.log`

#### 3. Database Connection Error

**Solution:**
- Verify PostgreSQL is running
- Check database credentials in `.env`
- Ensure database exists: `CREATE DATABASE sistem_antrian;`
- Run migrations: `php artisan migrate`

#### 4. 401 Unauthenticated Error

**Solution:**
- Ensure token is set in Authorization header: `Bearer {token}`
- Check token hasn't been revoked (logout)
- Verify token is from `/auth/google` login

#### 5. 422 Validation Error

**Solution:**
- Check request body matches validation rules
- Ensure all required fields are provided
- Verify data types (string, integer, etc.)
- Read error message for specific field errors

---

## 📝 Environment Variables

### Required Variables

```env
# Application
APP_NAME="Sistem Antrian Service"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sistem_antrian
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Google OAuth
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT=http://localhost:8000/api/auth/google/callback

# Email (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Antrian Service"
```

---

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Use HTTPS for all URLs
- [ ] Update Google OAuth redirect URI to production URL
- [ ] Use production email service (AWS SES, SendGrid, etc.)
- [ ] Set up proper database backups
- [ ] Configure CORS for frontend domain
- [ ] Set up SSL certificate
- [ ] Enable rate limiting
- [ ] Set up monitoring and logging

---

## 📖 Additional Documentation

For detailed setup guides, see:

- `GOOGLE_OAUTH_SETUP.md` - Complete Google OAuth configuration
- `GMAIL_SMTP_SETUP.md` - Email setup with Gmail SMTP
- `POSTMAN_GUIDE.md` - Postman collection usage guide

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👥 Team

Developed for WST (Web Service Technology) Course - Semester 7

---

**Happy Coding! 🚀**
