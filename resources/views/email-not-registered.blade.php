<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Belum Terdaftar - Sistem Antrian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            color: #ff6b6b;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        h1 {
            color: #2d3748;
            font-size: 28px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .message {
            color: #718096;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .info-box {
            background: #fffaf0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #fcb69f;
        }

        .info-box p {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 8px;
            text-align: left;
        }

        .info-box p:last-child {
            margin-bottom: 0;
        }

        .info-box strong {
            color: #2d3748;
            font-weight: 600;
        }

        .email-display {
            background: #f7fafc;
            padding: 12px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            color: #2d3748;
            font-weight: 600;
            margin: 20px 0;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 14px;
        }

        .contact-box {
            background: #f7fafc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .contact-box p {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .contact-box a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <span>🔒</span>
        </div>
        
        <h1>Email Belum Terdaftar</h1>
        
        <p class="message">
            Email yang Anda gunakan untuk login belum terdaftar di sistem. Silakan hubungi administrator untuk mendaftarkan akun Anda.
        </p>

        @if(isset($email))
        <div class="email-display">
            {{ $email }}
        </div>
        @endif

        <div class="info-box">
            <p><strong>Kenapa ini terjadi?</strong></p>
            <p style="margin-top: 10px;">
                Sistem Antrian Service menggunakan sistem registrasi yang dikelola oleh administrator. 
                Hanya pengguna yang telah didaftarkan oleh admin yang dapat login ke sistem.
            </p>
        </div>

        <div class="contact-box">
            <p><strong>Hubungi Administrator:</strong></p>
            <p>Email: <a href="mailto:admin@sistem-antrian.com">admin@sistem-antrian.com</a></p>
            <p style="margin-top: 15px; font-size: 13px; color: #718096;">
                Sertakan email Anda ({{ $email ?? 'email Anda' }}) saat menghubungi administrator untuk mempercepat proses registrasi.
            </p>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ config('app.url') }}" class="button">
                Kembali ke Beranda
            </a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Sistem Antrian Service. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
