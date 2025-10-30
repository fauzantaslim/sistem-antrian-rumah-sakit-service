# 📧 Gmail SMTP Setup untuk Email Verification

Panduan lengkap untuk setup Gmail SMTP agar email verification bisa terkirim.

---

## 🔐 Step 1: Generate Gmail App Password

### Kenapa Perlu App Password?
Gmail tidak mengizinkan login dengan password biasa untuk aplikasi eksternal. Anda harus menggunakan **App Password**.

### Cara Generate App Password:

1. **Buka Google Account Settings**
   - Kunjungi: https://myaccount.google.com/
   - Login dengan akun Gmail Anda

2. **Enable 2-Step Verification** (jika belum)
   - Klik **Security** di sidebar kiri
   - Scroll ke bawah, cari **2-Step Verification**
   - Klik **Get Started** dan ikuti instruksi
   - **PENTING:** App Password hanya bisa dibuat jika 2-Step Verification sudah aktif

3. **Generate App Password**
   - Masih di halaman **Security**
   - Scroll ke bawah, cari **App passwords**
   - Klik **App passwords**
   - Pilih app: **Mail**
   - Pilih device: **Other (Custom name)**
   - Ketik nama: `Laravel Sistem Antrian`
   - Klik **Generate**
   - **Copy 16-digit password** yang muncul (contoh: `abcd efgh ijkl mnop`)
   - **PENTING:** Password ini hanya muncul sekali, simpan dengan aman!

---

## ⚙️ Step 2: Update File `.env`

Buka file `.env` di root project dan update konfigurasi email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Antrian Service"
```

**Ganti:**
- `your-email@gmail.com` → Email Gmail Anda
- `abcdefghijklmnop` → 16-digit App Password (tanpa spasi)

**Contoh:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=fauzantaslim123@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=fauzantaslim123@gmail.com
MAIL_FROM_NAME="Sistem Antrian Service"
```

---

## 🧪 Step 3: Test Email Configuration

### Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Test Kirim Email
Buat test route untuk cek apakah email bisa terkirim:

**File: `routes/web.php`**
```php
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    $data = [
        'name' => 'Test User',
        'message' => 'This is a test email from Laravel.'
    ];

    Mail::raw('Test email from Laravel', function ($message) {
        $message->to('recipient@example.com')
                ->subject('Test Email');
    });

    return 'Email sent!';
});
```

**Akses di browser:**
```
http://localhost:8000/test-email
```

Cek inbox email recipient, seharusnya ada email masuk.

---

## 📨 Step 4: Test Email Verification

### Create User via API
```bash
POST http://localhost:8000/api/users
Content-Type: application/json
Authorization: Bearer {your_access_token}

{
  "full_name": "Test User",
  "email": "testuser@gmail.com",
  "role": "petugas",
  "counter_id": "01K8SQ4WFRZZKRQZVDEGGCDCJ9"
}
```

### Cek Email
1. Buka inbox email `testuser@gmail.com`
2. Seharusnya ada email dengan subject: **"Verifikasi Email Anda - Sistem Antrian"**
3. Klik tombol **"Verifikasi Email"**
4. Email akan terverifikasi

---

## 🐛 Troubleshooting

### Error: "Failed to authenticate on SMTP server"
**Penyebab:**
- App Password salah
- 2-Step Verification belum aktif
- Username/password ada spasi

**Solusi:**
1. Generate ulang App Password
2. Pastikan tidak ada spasi di `MAIL_PASSWORD`
3. Cek `MAIL_USERNAME` sama dengan email Gmail Anda
4. Jalankan `php artisan config:clear`

---

### Error: "Connection could not be established with host smtp.gmail.com"
**Penyebab:**
- Port salah
- Firewall/antivirus block koneksi
- Internet tidak stabil

**Solusi:**
1. Pastikan `MAIL_PORT=587` dan `MAIL_ENCRYPTION=tls`
2. Atau coba port 465 dengan `MAIL_ENCRYPTION=ssl`
3. Disable firewall/antivirus sementara untuk testing
4. Cek koneksi internet

---

### Email Masuk ke Spam
**Solusi:**
1. Tandai email sebagai "Not Spam"
2. Tambahkan sender ke contact list
3. Untuk production, gunakan domain email sendiri (bukan Gmail)

---

### Email Tidak Terkirim (No Error)
**Cek:**
1. Log Laravel: `storage/logs/laravel.log`
2. Pastikan `MAIL_MAILER=smtp` (bukan `log`)
3. Jalankan `php artisan config:clear`
4. Cek queue jika menggunakan queue: `php artisan queue:work`

---

## 📝 Alternative: Mailtrap (Development)

Untuk development, Anda bisa gunakan **Mailtrap** (tidak perlu Gmail):

1. Daftar di https://mailtrap.io (gratis)
2. Buat inbox baru
3. Copy credentials ke `.env`:

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

**Keuntungan Mailtrap:**
- Email tidak benar-benar terkirim (aman untuk testing)
- Bisa lihat preview email di dashboard
- Tidak perlu App Password
- Tidak ada limit untuk development

---

## 🚀 Production Recommendations

Untuk production, **JANGAN gunakan Gmail SMTP**. Gunakan:

1. **AWS SES** (Amazon Simple Email Service)
2. **SendGrid**
3. **Mailgun**
4. **Postmark**
5. **Domain email sendiri** dengan SMTP server

**Alasan:**
- Gmail punya limit pengiriman (500 email/hari)
- Sering masuk spam
- Tidak profesional
- Bisa kena suspend jika dianggap spam

---

## ✅ Checklist

- [ ] 2-Step Verification aktif di Google Account
- [ ] App Password sudah di-generate
- [ ] File `.env` sudah diupdate dengan credentials
- [ ] Config cache sudah di-clear
- [ ] Test email berhasil terkirim
- [ ] Email verification berhasil dikirim saat create user
- [ ] Email verification link berfungsi

---

**Happy Coding! 📧**
