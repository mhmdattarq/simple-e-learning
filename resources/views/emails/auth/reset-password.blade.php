<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - SIMPEL BKPSDM Aceh Timur</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 16px;
        }
        .email-card {
            max-width: 580px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background-color: #071a33;
            padding: 32px 36px;
            text-align: center;
        }
        .header-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            color: #94a3b8;
            font-size: 13px;
            margin: 0;
        }
        .email-body {
            padding: 36px 36px 28px;
        }
        .greeting {
            font-size: 17px;
            font-weight: 600;
            color: #0f172a;
            margin: 0 0 16px;
        }
        .text-paragraph {
            font-size: 14.5px;
            line-height: 1.65;
            color: #475569;
            margin: 0 0 20px;
        }
        .security-badge {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 13.5px;
            color: #92400e;
            line-height: 1.5;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-reset {
            display: inline-block;
            background-color: #071a33;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 14.5px;
            padding: 14px 34px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(7, 26, 51, 0.25);
        }
        .link-fallback {
            font-size: 12px;
            line-height: 1.5;
            color: #94a3b8;
            margin-top: 24px;
            word-break: break-all;
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 6px;
        }
        .link-fallback a {
            color: #0284c7;
        }
        .email-footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 24px 36px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .email-footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <div class="email-header">
                <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo Aceh Timur" width="48" height="48" style="display: block; margin: 0 auto 12px; object-fit: contain;">
                <h1 class="header-title">SIMPEL E-LEARNING</h1>
                <p class="header-subtitle">Badan Kepegawaian dan Pengembangan SDM Kabupaten Aceh Timur</p>
            </div>
            <div class="email-body">
                <p class="greeting">Yth. Bapak/Ibu {{ $user->name }},</p>
                <p class="text-paragraph">
                    Kami menerima permintaan pengaturan ulang kata sandi untuk akun Portal SIMPEL Anda yang terdaftar dengan email: <strong>{{ $user->email }}</strong>.
                </p>
                <p class="text-paragraph">
                    Silakan klik tombol di bawah ini untuk membuat kata sandi baru Anda:
                </p>

                <div class="btn-wrapper">
                    <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">
                        Atur Ulang Kata Sandi Saya
                    </a>
                </div>

                <div class="security-badge">
                    <strong>Pemberitahuan Keamanan:</strong><br>
                    • Tautan ini hanya berlaku selama <strong>60 menit</strong> sejak email ini dikirimkan.<br>
                    • Tautan ini hanya dapat digunakan <strong>1 (satu) kali</strong>.<br>
                    • Jika Anda tidak pernah meminta perubahan kata sandi, abaikan email ini. Akun Anda tetap aman dan kata sandi Anda tidak akan berubah.
                </div>

                <div class="link-fallback">
                    Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut ke peramban (browser) Anda:<br>
                    <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
                </div>
            </div>
            <div class="email-footer">
                <p><strong>BKPSDM Kabupaten Aceh Timur</strong></p>
                <p>Pusat Pemerintahan Kabupaten Aceh Timur · Idi Rayeuk</p>
                <p>Aksi Perubahan Kinerja 2026 · Efektif, Efisien, Transparan, Akuntabel</p>
            </div>
        </div>
    </div>
</body>
</html>
