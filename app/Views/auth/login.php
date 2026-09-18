<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk &bull; <?= esc($namaSekolah ?? 'Supervisi Guru') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #17202A;
            background-color: #F7F4EC;
            -webkit-font-smoothing: antialiased;
        }

        /* Split-screen 2 kolom (55% Kiri : 45% Kanan) */
        .login-split-container {
            width: 100%;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            margin: 0;
            padding: 0;
        }

        /* SISI KIRI (Dark Cinematic Library dengan Foto & Kutipan) */
        .login-split-left {
            position: relative;
            background: #0B1117 url('<?= base_url('assets/img/login-bg.png') ?>') center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem 4rem;
            color: #ffffff;
            overflow: hidden;
        }

        /* Overlay filmis halus agar foto terlihat jelas tapi teks 100% kontras */
        .login-split-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                145deg,
                rgba(11, 17, 23, 0.80) 0%,
                rgba(23, 35, 49, 0.65) 50%,
                rgba(11, 17, 23, 0.85) 100%
            );
            z-index: 1;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        /* Brand Top Kiri */
        .login-brand-top {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .login-left-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 12px;
            background: #ffffff;
            padding: 4px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            flex: none;
        }

        .login-left-brand-text strong {
            display: block;
            color: #ffffff !important;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            line-height: 1.2;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7);
        }

        .login-left-brand-text small {
            display: block;
            color: #D9B36C !important;
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 0.15rem;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
        }

        /* Quote Area Tengah */
        .login-left-quote {
            max-width: 500px;
            margin: auto 0;
            padding: 2.5rem 0;
        }

        .login-left-quote-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.3rem, 3.8vw, 3.1rem);
            font-style: italic;
            font-weight: 700;
            color: #ffffff !important;
            line-height: 1.2;
            margin: 0 0 1.25rem;
            letter-spacing: -0.01em;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.95), 0 2px 6px rgba(0, 0, 0, 0.9);
        }

        .login-left-gold-bar {
            width: 48px;
            height: 3.5px;
            background: #D9B36C;
            border-radius: 2px;
            margin-bottom: 1.35rem;
            box-shadow: 0 2px 8px rgba(217, 179, 108, 0.5);
        }

        .login-left-quote-sub {
            font-size: 1.1rem;
            color: #F7F4EC !important;
            line-height: 1.65;
            margin: 0;
            font-weight: 500;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.9);
        }

        /* Seni Punggung Buku Kulit (Sesuai Gambar Referensi) */
        .login-left-books {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-top: 2rem;
            max-width: 290px;
        }

        .book-tag {
            background: linear-gradient(90deg, rgba(20, 29, 43, 0.9) 0%, rgba(30, 44, 63, 0.9) 100%);
            border: 1px solid rgba(217, 179, 108, 0.45);
            border-radius: 4px;
            padding: 0.45rem 1rem;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 0.72rem;
            letter-spacing: 0.22em;
            color: #E8C98F;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-transform: uppercase;
        }

        /* Footer Bawah Kiri */
        .login-left-tags {
            font-size: 0.84rem;
            color: #cbd5e1;
            letter-spacing: 0.08em;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.7);
        }

        /* SISI KANAN (Warm Ivory Surface dengan Kartu Putih Terpusat) */
        .login-split-right {
            background-color: #F7F4EC; /* Warm Ivory sesuai spesifikasi */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 2.5rem;
            position: relative;
        }

        .login-box-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid #E6E0D4;
            box-shadow: 0 20px 45px -10px rgba(23, 32, 42, 0.08), 0 2px 6px -1px rgba(23, 32, 42, 0.04);
            padding: 2.75rem 2.25rem;
            position: relative;
            box-sizing: border-box;
        }

        .login-back-link {
            position: absolute;
            top: 1.25rem;
            right: 1.5rem;
            color: #69727C;
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.15s ease;
        }

        .login-back-link:hover {
            color: #B58C42;
            text-decoration: none;
        }

        .login-card-header {
            text-align: center;
            margin-bottom: 2rem;
            margin-top: 0.5rem;
        }

        .login-card-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            border-radius: 14px;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border: 1.5px solid #E6E0D4;
            margin: 0 auto 1.15rem;
            display: block;
        }

        .login-card-header h2 {
            font-size: 1.55rem;
            font-weight: 800;
            color: #17202A;
            margin: 0 0 0.35rem;
            letter-spacing: -0.02em;
        }

        .login-card-header p {
            color: #69727C;
            font-size: 0.88rem;
            margin: 0;
        }

        /* Form Inputs */
        .login-field-wrap {
            margin-bottom: 1.2rem;
        }

        .login-input-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-input-ico {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
        }

        .login-field-control {
            width: 100%;
            height: 48px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            padding: 0 2.75rem 0 2.85rem;
            font-size: 0.92rem;
            color: #17202A;
            background: #ffffff;
            font-family: inherit;
            outline: none;
            transition: all 0.15s ease;
        }

        .login-field-control::placeholder {
            color: #94a3b8;
        }

        .login-field-control:focus {
            border-color: #D9B36C;
            box-shadow: 0 0 0 4px rgba(217, 179, 108, 0.18);
        }

        .login-field-control.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
        }

        .login-eye-btn {
            position: absolute;
            right: 0.65rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.4rem 0.6rem;
            font-size: 0.95rem;
            display: grid;
            place-items: center;
            border-radius: 6px;
        }

        .login-eye-btn:hover {
            color: #17202A;
        }

        .login-options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
            font-size: 0.85rem;
        }

        .login-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #69727C;
            font-weight: 500;
            cursor: pointer;
            margin: 0;
        }

        .login-forgot-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .login-forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login-gold {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #E8C98F, #D9B36C);
            color: #0B1117;
            border: 1px solid #E8C98F;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(217, 179, 108, 0.35);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-login-gold:hover {
            background: linear-gradient(135deg, #D9B36C, #B58C42);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(217, 179, 108, 0.48);
        }

        .btn-login-gold:disabled {
            opacity: 0.7;
            cursor: wait;
            transform: none;
        }

        .login-or-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.6rem 0 1.2rem;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .login-or-separator::before,
        .login-or-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E2E8F0;
        }

        .login-or-separator span {
            padding: 0 0.85rem;
        }

        .btn-oauth {
            width: 100%;
            height: 44px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 0 1rem;
            background: #ffffff;
            color: #17202A;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-oauth:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            text-decoration: none;
            color: #17202A;
        }

        .login-help-text {
            text-align: center;
            margin-top: 1.4rem;
            font-size: 0.8rem;
            color: #69727C;
        }

        .login-alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            line-height: 1.5;
        }

        /* Responsif Tablet & Mobile */
        @media (max-width: 1024px) {
            .login-split-container {
                grid-template-columns: 1fr;
            }
            .login-split-left {
                min-height: auto;
                padding: 3rem 2rem 2.5rem;
                text-align: center;
                align-items: center;
            }
            .login-left-gold-bar {
                margin: 0 auto 1.25rem;
            }
            .login-left-books,
            .login-left-tags {
                display: none;
            }
            .login-split-right {
                padding: 2rem 1.5rem 3.5rem;
            }
        }
    </style>
</head>
<body>

<?php
$ident   = $identitas ?? [];
$nama    = $namaSekolah ?? ($ident['nama_sekolah'] ?? 'Supervisi Guru');
$logo    = $ident['logo_url'] ?? base_url('assets/img/logo-placeholder.svg');
$hasErr  = session()->getFlashdata('error') || isset($validation);
?>

<div class="login-split-container">
    <!-- SISI KIRI (Dark Cinematic Library dengan Foto & Kutipan) -->
    <div class="login-split-left">
        <div class="login-left-content">
            <!-- Brand Top Kiri -->
            <div class="login-brand-top">
                <img src="<?= esc($logo, 'attr') ?>"
                     onerror="this.onerror=null;this.src='<?= base_url('assets/img/logo-placeholder.svg') ?>'"
                     alt="Logo <?= esc($nama, 'attr') ?>"
                     class="login-left-logo">
                <div class="login-left-brand-text">
                    <strong><?= esc($nama) ?></strong>
                    <small>Bersama Mewujudkan Pendidikan Berkualitas</small>
                </div>
            </div>

            <!-- Area Kutipan Utama -->
            <div class="login-left-quote">
                <h1 class="login-left-quote-title">
                    “Guru Hebat Melahirkan Generasi Luar Biasa”
                </h1>
                <div class="login-left-gold-bar"></div>
                <p class="login-left-quote-sub">
                    Satu langkah kecil untuk perubahan besar dalam dunia pendidikan.
                </p>

                <!-- 4 Punggung Buku Kulit Emas (Persis Gambar Referensi) -->
                <div class="login-left-books">
                    <div class="book-tag">PENDIDIKAN</div>
                    <div class="book-tag">PROFESIONALISME</div>
                    <div class="book-tag">INSPIRASI</div>
                    <div class="book-tag">MASA DEPAN</div>
                </div>
            </div>

            <!-- Footer Bawah Kiri -->
            <div class="login-left-tags">
                Integritas &nbsp;|&nbsp; Kolaborasi &nbsp;|&nbsp; Profesional &nbsp;|&nbsp; Berkelanjutan
            </div>
        </div>
    </div>

    <!-- SISI KANAN (Warm Ivory Canvas dengan Kartu Putih Melayang Presisi) -->
    <div class="login-split-right">
        <div class="login-box-card">
            <!-- Navigasi Kembali ke Beranda -->
            <a href="<?= base_url('/') ?>" class="login-back-link">
                <span>Kembali ke Beranda</span>
                <i class="fas fa-arrow-right fa-xs"></i>
            </a>

            <!-- Logo Sekolah & Judul Card -->
            <div class="login-card-header">
                <img src="<?= esc($logo, 'attr') ?>"
                     onerror="this.onerror=null;this.src='<?= base_url('assets/img/logo-placeholder.svg') ?>'"
                     alt="Logo <?= esc($nama, 'attr') ?>"
                     class="login-card-logo">
                <h2>Selamat Datang</h2>
                <p>Masuk untuk mengakses sistem <?= esc($nama) ?></p>
            </div>

            <!-- Pesan Error / Validasi -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="login-alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="login-alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <!-- Formulir Login -->
            <form id="loginForm" action="<?= base_url('auth/attemptLogin') ?>" method="post" novalidate>
                <?= csrf_field() ?>

                <!-- Input Username / Email -->
                <div class="login-field-wrap">
                    <div class="login-input-box">
                        <i class="fas fa-envelope login-input-ico"></i>
                        <input class="login-field-control<?= $hasErr ? ' is-invalid' : '' ?>" 
                               type="text" 
                               id="login" 
                               name="login"
                               placeholder="Email atau Username" 
                               value="<?= esc(old('login') ?? old('email') ?? '') ?>"
                               required 
                               autocomplete="username" 
                               autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="login-field-wrap">
                    <div class="login-input-box">
                        <i class="fas fa-lock login-input-ico"></i>
                        <input class="login-field-control<?= $hasErr ? ' is-invalid' : '' ?>" 
                               type="password" 
                               id="password" 
                               name="password"
                               placeholder="Password" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="login-eye-btn" id="togglePassword" aria-label="Lihat password">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Opsi Ingat Saya & Lupa Password -->
                <div class="login-options-row">
                    <label class="login-checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya</span>
                    </label>
                    <a href="javascript:void(0)" onclick="alert('Silakan hubungi Administrator sekolah untuk reset kata sandi Anda.')" class="login-forgot-link">
                        Lupa password?
                    </a>
                </div>

                <!-- Tombol Masuk Emas -->
                <button type="submit" class="btn-login-gold" id="submitBtn">
                    Masuk
                </button>

                <!-- Pemisah "atau masuk dengan" -->
                <div class="login-or-separator">
                    <span>atau masuk dengan</span>
                </div>

                <!-- Tombol SSO Google & Microsoft (Sesuai Referensi) -->
                <a href="javascript:void(0)" onclick="alert('Integrasi Single Sign-On (SSO) Google sedang dipersiapkan oleh administrator sekolah.')" class="btn-oauth">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.26v3.15C3.25 21.37 7.32 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.26C.46 8.17 0 9.99 0 12s.46 3.83 1.26 5.42l4.02-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.32 0 3.25 2.63 1.26 6.58l4.02 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>

                <a href="javascript:void(0)" onclick="alert('Integrasi Single Sign-On (SSO) Microsoft sedang dipersiapkan oleh administrator sekolah.')" class="btn-oauth">
                    <svg width="18" height="18" viewBox="0 0 23 23">
                        <path fill="#f35325" d="M1 1h10v10H1z"/>
                        <path fill="#81bc06" d="M12 1h10v10H12z"/>
                        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                        <path fill="#ffba08" d="M12 12h10v10H12z"/>
                    </svg>
                    <span>Masuk dengan Microsoft</span>
                </a>

                <div class="login-help-text">
                    Belum punya akun? Hubungi administrator sekolah Anda.
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('togglePassword');
    var passwordField = document.getElementById('password');
    var eyeIcon = document.getElementById('eyeIcon');
    var form = document.getElementById('loginForm');
    var submitBtn = document.getElementById('submitBtn');

    if (toggleBtn && passwordField && eyeIcon) {
        toggleBtn.addEventListener('click', function() {
            var isPassword = passwordField.getAttribute('type') === 'password';
            passwordField.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            passwordField.focus();
        });
    }

    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Memproses...';
        });
    }
});
</script>
</body>
</html>
