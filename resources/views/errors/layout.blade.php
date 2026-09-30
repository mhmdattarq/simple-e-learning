<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIMPEL BKPSDM Aceh Timur</title>
    <link rel="icon" type="image/webp" href="{{ asset('mine/logo_aceh_timur.webp') }}" />
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/css/remixicon.css') }}">

    <style>
        :root {
            --simpel-navy: #0f172a;
            --simpel-navy-light: #1e293b;
            --simpel-gold: #e5a93b;
            --simpel-gold-hover: #d29427;
        }

        body {
            font-family: 'Outfit', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #090e17 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }

        .error-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            width: 100%;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e5a93b, #f59e0b, #e5a93b);
        }

        .brand-logo {
            width: 56px;
            height: auto;
            margin-bottom: 1.25rem;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
        }

        .error-code-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(229, 169, 59, 0.15);
            border: 1px solid rgba(229, 169, 59, 0.3);
            color: var(--simpel-gold);
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
        }

        .error-icon-wrapper {
            width: 88px;
            height: 88px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.75rem;
        }

        .error-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.75rem;
        }

        .error-desc {
            font-size: 1rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-simpel-primary {
            background-color: var(--simpel-gold);
            border-color: var(--simpel-gold);
            color: var(--simpel-navy);
            font-weight: 700;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-simpel-primary:hover {
            background-color: var(--simpel-gold-hover);
            border-color: var(--simpel-gold-hover);
            color: var(--simpel-navy);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(229, 169, 59, 0.3);
        }

        .btn-simpel-outline {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-simpel-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }

        .footer-note {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.8125rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <a href="{{ url('/') }}" title="SIMPEL BKPSDM Aceh Timur">
            <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo Aceh Timur" class="brand-logo">
        </a>

        <div>
            <span class="error-code-badge">
                <i class="@yield('badge-icon', 'ri-error-warning-line')"></i>
                KODE ERROR @yield('code')
            </span>
        </div>

        <div class="error-icon-wrapper @yield('icon-bg', 'bg-warning-subtle text-warning')">
            <i class="@yield('icon', 'ri-alert-line')"></i>
        </div>

        <h1 class="error-title">@yield('title')</h1>
        <p class="error-desc">@yield('message')</p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ url('/') }}" class="btn-simpel-primary">
                <i class="ri-home-4-line"></i> Kembali ke Beranda
            </a>
            <button onclick="window.location.reload();" class="btn-simpel-outline">
                <i class="ri-refresh-line"></i> Muat Ulang
            </button>
        </div>

        <div class="footer-note">
            SIMPEL — Sistem Informasi Manajemen Pembelajaran Elektronik<br>
            BKPSDM Kabupaten Aceh Timur
        </div>
    </div>
</body>
</html>
