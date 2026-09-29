<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun SIMPEL BKPSDM Aceh Timur</title>
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
        .seal-logo {
            display: inline-block;
            width: 48px;
            height: 48px;
            line-height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f3bc42 0%, #d49818 100%);
            color: #071a33;
            font-size: 24px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 12px;
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
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #f3bc42;
            padding: 16px 20px;
            border-radius: 8px;
            margin: 24px 0;
        }
        .info-row {
            font-size: 13px;
            margin: 4px 0;
            color: #475569;
        }
        .info-row strong {
            color: #0f172a;
            display: inline-block;
            min-width: 100px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .btn-verify {
            display: inline-block;
            background-color: #071a33;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 600;
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
                    Terima kasih telah mendaftarkan akun di Portal SIMPEL (Sistem Informasi Manajemen Pembelajaran Elektronik) BKPSDM Kabupaten Aceh Timur.
                </p>
                <p class="text-paragraph">
                    Untuk menjamin keabsahan data aparatur dan mengamankan akun Anda, silakan aktifkan akun Anda dengan mengklik tombol verifikasi di bawah ini:
                </p>

                <div class="info-box">
                    <div class="info-row"><strong>NIP:</strong> {{ $user->nip ?? '-' }}</div>
                    <div class="info-row"><strong>Email:</strong> {{ $user->email }}</div>
                    <div class="info-row"><strong>Instansi/OPD:</strong> {{ $user->opd_agency ?? '-' }}</div>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ $verifyUrl }}" class="btn-verify" target="_blank">
                        Aktifkan & Verifikasi Email Saya
                    </a>
                </div>

                <p class="text-paragraph" style="font-size: 13px; color: #64748b;">
                    <em>Tautan verifikasi ini bersifat rahasia dan hanya berlaku selama <strong>30 menit</strong>. Jika Anda tidak merasa melakukan pendaftaran akun ini, Anda dapat mengabaikan email ini.</em>
                </p>

                <div class="link-fallback">
                    Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut pada peramban Anda:<br>
                    <a href="{{ $verifyUrl }}">{{ $verifyUrl }}</a>
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
