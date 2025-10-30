<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Belum Terverifikasi - Sistem Antrian</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
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
            background: #fff5f5;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #f5576c;
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

        .info-box ul {
            margin: 10px 0 0 20px;
            text-align: left;
        }

        .info-box li {
            margin-bottom: 8px;
            color: #4a5568;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            margin: 5px;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
        }

        .button.secondary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .button.secondary:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
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

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 14px;
        }

        .steps {
            text-align: left;
            margin: 20px 0;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .step-number {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
            padding-top: 5px;
        }

        .step-content p {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <span>✉</span>
        </div>
        
        <h1>Email Belum Terverifikasi</h1>
        
        <p class="message">
            Akun Anda sudah terdaftar, namun email belum diverifikasi. Silakan verifikasi email Anda terlebih dahulu sebelum login.
        </p>

        @if(isset($email))
        <div class="email-display">
            {{ $email }}
        </div>
        @endif

        <div class="info-box">
            <p><strong>Langkah-langkah Verifikasi:</strong></p>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <p>Buka inbox email Anda</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <p>Cari email dengan subject "Verifikasi Email Anda - Sistem Antrian"</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <p>Klik tombol "Verifikasi Email" di dalam email</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <p>Setelah terverifikasi, Anda dapat login</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            @if(isset($user_id))
            <a href="{{ config('app.url') }}/api/users/{{ $user_id }}/resend-verification" class="button">
                Kirim Ulang Email Verifikasi
            </a>
            @endif
            
            <a href="{{ config('app.url') }}" class="button secondary">
                Kembali ke Beranda
            </a>
        </div>

        <div class="footer">
            <p><strong>Tidak menerima email?</strong></p>
            <p style="margin-top: 10px;">Cek folder Spam/Junk atau hubungi administrator</p>
            <p style="margin-top: 20px;">&copy; {{ date('Y') }} Sistem Antrian Service. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
