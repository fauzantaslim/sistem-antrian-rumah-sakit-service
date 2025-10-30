<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - Sistem Antrian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        }

        .icon.success {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        .icon.error {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            animation: scaleIn 0.5s ease-out 0.2s both;
        }

        .icon.warning {
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
            background: #f7fafc;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .info-box p {
            color: #4a5568;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .info-box p:last-child {
            margin-bottom: 0;
        }

        .info-box strong {
            color: #2d3748;
            font-weight: 600;
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

        .checkmark {
            display: inline-block;
            animation: checkmark 0.5s ease-out 0.4s both;
        }

        @keyframes checkmark {
            0% {
                transform: scale(0) rotate(-45deg);
            }
            50% {
                transform: scale(1.2) rotate(-45deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @if($status === 'success')
            <div class="icon success">
                <span class="checkmark">✓</span>
            </div>
            <h1>Email Berhasil Diverifikasi!</h1>
            <p class="message">
                Selamat! Email Anda telah berhasil diverifikasi. Sekarang Anda dapat login ke sistem menggunakan akun Anda.
            </p>
            
            @if(isset($user))
            <div class="info-box">
                <p><strong>Nama:</strong> {{ $user->full_name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
            </div>
            @endif

            <a href="{{ config('app.url') }}/api/auth/google" class="button">
                Login Sekarang
            </a>

        @elseif($status === 'already_verified')
            <div class="icon warning">
                <span>ℹ</span>
            </div>
            <h1>Email Sudah Terverifikasi</h1>
            <p class="message">
                Email Anda sudah terverifikasi sebelumnya. Anda dapat langsung login ke sistem.
            </p>

            <a href="{{ config('app.url') }}/api/auth/google" class="button">
                Login Sekarang
            </a>

        @else
            <div class="icon error">
                <span>✕</span>
            </div>
            <h1>Verifikasi Gagal</h1>
            <p class="message">
                Maaf, terjadi kesalahan saat memverifikasi email Anda. Link verifikasi mungkin sudah kedaluwarsa atau tidak valid.
            </p>

            <div class="info-box">
                <p><strong>Apa yang harus dilakukan?</strong></p>
                <p>Silakan hubungi administrator untuk mendapatkan link verifikasi baru.</p>
            </div>

            <a href="{{ config('app.url') }}" class="button">
                Kembali ke Beranda
            </a>
        @endif

        <div class="footer">
            <p>&copy; {{ date('Y') }} Sistem Antrian Service. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
