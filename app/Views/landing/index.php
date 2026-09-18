<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($namaSekolah ?? 'Supervisi Guru') ?> &bull; Bersama Mewujudkan Pendidikan Berkualitas</title>
    <meta name="description" content="Platform digital Supervisi Guru untuk membantu sekolah merencanakan, melaksanakan, mengevaluasi, dan mendokumentasikan supervisi secara terstruktur.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        :root {
            --c-dark: #0B1117;
            --c-navy: #172331;
            --c-bluegray: #344454;
            --c-ivory: #F7F4EC;
            --c-warmwhite: #FCFBF8;
            --c-gold: #D9B36C;
            --c-gold-soft: #E8C98F;
            --c-gold-dark: #B58C42;
            --c-text-dark: #17202A;
            --c-text-muted: #69727C;
            --c-border-light: #E6E0D4;
            --font-serif: 'Playfair Display', Georgia, serif;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
            color: var(--c-text-dark);
            background-color: var(--c-dark);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* 4.1 Navbar (Sticky, Glass Dark) */
        .lux-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(11, 17, 23, 0.85);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
            transition: all 0.3s ease;
        }

        .lux-nav-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0.95rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .lux-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            text-decoration: none;
        }

        .lux-brand-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: contain;
            background: #ffffff;
            padding: 3px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.2);
            flex: none;
        }

        .lux-brand-text strong {
            display: block;
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .lux-brand-text small {
            display: block;
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 0.1rem;
        }

        .lux-nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .lux-nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .lux-nav-links a:hover,
        .lux-nav-links a.active {
            color: var(--c-gold);
        }

        .lux-btn-nav {
            background: linear-gradient(135deg, var(--c-gold-soft), var(--c-gold));
            color: #0b1117 !important;
            font-weight: 800;
            font-size: 0.88rem;
            padding: 0.6rem 1.6rem;
            border-radius: 9999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 4px 14px rgba(217, 179, 108, 0.35);
            transition: all 0.2s ease;
            border: 1px solid var(--c-gold-soft);
        }

        .lux-btn-nav:hover {
            background: linear-gradient(135deg, var(--c-gold), var(--c-gold-dark));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(217, 179, 108, 0.5);
            text-decoration: none;
            color: #0b1117 !important;
        }

        /* ==========================================================================
           4.2 HERO SECTION: WIDE BACKGROUND IMAGE + TRANSPARENT GLASS LAYER
           ========================================================================== */

        .lux-hero-wide {
            position: relative;
            width: 100%;
            min-height: 88vh;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Gambar lebar di belakang sebagai background */
            background: #0B1117 url('<?= base_url('assets/img/landing-hero.png') ?>') center center / cover no-repeat;
            padding: 6.5rem 2rem 6.5rem;
            overflow: hidden;
        }

        /* Layer depan transparan / semi-transparan sinematik agar gambar terlihat jelas & teks kontras */
        .lux-hero-wide::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(11, 17, 23, 0.68) 0%,
                rgba(15, 23, 42, 0.50) 45%,
                rgba(11, 17, 23, 0.78) 80%,
                rgba(11, 17, 23, 0.98) 100%
            );
            pointer-events: none;
            z-index: 1;
        }

        /* Vignette radial untuk nuansa filmis mewah */
        .lux-hero-wide::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, transparent 30%, rgba(8, 12, 22, 0.65) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .lux-hero-content-wide {
            position: relative;
            z-index: 2;
            max-width: 980px;
            margin: 0 auto;
            text-align: center;
        }

        /* Eyebrow Glass Capsule */
        .lux-hero-glass-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(11, 17, 23, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(217, 179, 108, 0.4);
            border-radius: 9999px;
            padding: 0.45rem 1.35rem;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            color: var(--c-gold);
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        /* Grand Serif Headline */
        .lux-hero-h1-wide {
            font-family: var(--font-serif);
            font-size: clamp(2.6rem, 5.2vw, 4.4rem);
            line-height: 1.12;
            font-weight: 700;
            color: #ffffff;
            margin: 0 auto 1.5rem;
            letter-spacing: -0.015em;
            max-width: 900px;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.95), 0 2px 6px rgba(0, 0, 0, 0.9);
        }

        .lux-hero-h1-wide .gold-accent {
            color: var(--c-gold-soft);
            font-style: italic;
        }

        .lux-hero-p-wide {
            font-size: 1.15rem;
            line-height: 1.75;
            color: #f1f5f9;
            max-width: 660px;
            margin: 0 auto 2.5rem;
            font-weight: 400;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.95);
        }

        /* CTA Buttons */
        .lux-hero-actions-wide {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.15rem;
            flex-wrap: wrap;
            margin-bottom: 3.5rem;
        }

        .lux-btn-gold-large {
            background: linear-gradient(135deg, var(--c-gold-soft), var(--c-gold));
            color: #0b1117 !important;
            font-weight: 800;
            font-size: 1.02rem;
            padding: 0.95rem 2.4rem;
            border-radius: 9999px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            box-shadow: 0 8px 24px rgba(217, 179, 108, 0.45);
            transition: all 0.2s ease;
            border: 1px solid var(--c-gold-soft);
        }

        .lux-btn-gold-large:hover {
            background: linear-gradient(135deg, var(--c-gold), var(--c-gold-dark));
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(217, 179, 108, 0.6);
            color: #0b1117 !important;
            text-decoration: none;
        }

        .lux-btn-glass-large {
            background: rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: #ffffff !important;
            font-weight: 700;
            font-size: 1.02rem;
            padding: 0.92rem 2.2rem;
            border-radius: 9999px;
            text-decoration: none;
            border: 1.5px solid rgba(255, 255, 255, 0.35);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .lux-btn-glass-large:hover {
            background: rgba(255, 255, 255, 0.22);
            border-color: #ffffff;
            transform: translateY(-2px);
            color: #ffffff !important;
            text-decoration: none;
        }

        /* 3 Trust Badges Strip (Floating Glass) */
        .lux-trust-strip-glass {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        .lux-glass-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(11, 17, 23, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 9999px;
            padding: 0.6rem 1.35rem;
            font-size: 0.88rem;
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        .lux-glass-badge i {
            color: var(--c-gold);
            font-size: 0.95rem;
        }

        /* Floating Highlight Quote Bar (Below Hero Badges) */
        .lux-hero-quote-bar {
            background: rgba(11, 17, 23, 0.55);
            border: 1px solid rgba(217, 179, 108, 0.25);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 16px;
            padding: 1rem 1.75rem;
            max-width: 600px;
            margin: 0 auto;
            color: #f1f5f9;
            font-family: var(--font-serif);
            font-style: italic;
            font-size: 1.12rem;
            line-height: 1.55;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .lux-hero-quote-bar span {
            color: var(--c-gold-soft);
        }

        /* ==========================================================================
           WARM IVORY CANVAS (#F7F4EC) - Tentang Kami, Fitur, Alur, Manfaat
           ========================================================================== */

        .lux-ivory-surface {
            background-color: var(--c-ivory);
            color: var(--c-text-dark);
            position: relative;
            z-index: 10;
        }

        /* 4.4 About Section */
        .lux-about-section {
            padding: 6.5rem 2rem;
            max-width: 1240px;
            margin: 0 auto;
        }

        .lux-about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4.5rem;
            align-items: center;
        }

        .lux-sec-eyebrow {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            color: var(--c-gold-dark);
            text-transform: uppercase;
        }

        .lux-about-left h2 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            line-height: 1.2;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0.5rem 0 1.5rem;
        }

        .lux-about-left p {
            color: var(--c-text-muted);
            font-size: 1.02rem;
            line-height: 1.75;
            margin-bottom: 1.5rem;
        }

        .lux-quote-banner {
            border-left: 3.5px solid var(--c-gold);
            padding-left: 1.35rem;
            margin: 2rem 0;
            font-family: var(--font-serif);
            font-style: italic;
            font-size: 1.18rem;
            color: var(--c-gold-dark);
            line-height: 1.6;
        }

        .lux-btn-link-gold {
            color: var(--c-gold-dark);
            font-weight: 700;
            font-size: 0.92rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.45rem;
            border-radius: 9999px;
            border: 1.5px solid var(--c-gold);
            background: rgba(217, 179, 108, 0.12);
            transition: all 0.2s ease;
        }

        .lux-btn-link-gold:hover {
            background: var(--c-gold);
            color: #0b1117;
            text-decoration: none;
        }

        .lux-about-visual {
            position: relative;
        }

        .lux-about-photo-frame {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 16px 40px -10px rgba(23, 32, 42, 0.12);
            aspect-ratio: 16/10;
            background: #ffffff;
            border: 1px solid var(--c-border-light);
        }

        .lux-about-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .lux-about-floating-badge {
            position: absolute;
            top: 1.5rem;
            left: -1.5rem;
            background: #ffffff;
            border-radius: 14px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 10px 25px rgba(23, 32, 42, 0.10);
            border: 1px solid var(--c-border-light);
        }

        .badge-icon-small {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--c-gold);
            color: #0b1117;
            display: grid;
            place-items: center;
            font-size: 0.9rem;
            flex: none;
        }

        /* 4.5 Feature Section (4 Grid Cards) */
        .lux-features-section {
            padding: 2rem 2rem 6.5rem;
            max-width: 1240px;
            margin: 0 auto;
        }

        .lux-features-header {
            text-align: center;
            max-width: 620px;
            margin: 0 auto 3.5rem;
        }

        .lux-features-header h2 {
            font-family: var(--font-serif);
            font-size: 2.35rem;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0.4rem 0 0.75rem;
        }

        .lux-features-header p {
            color: var(--c-text-muted);
            font-size: 1rem;
            line-height: 1.6;
            margin: 0;
        }

        .lux-features-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.35rem;
        }

        .lux-feature-card {
            background: #ffffff;
            border: 1px solid var(--c-border-light);
            border-radius: 18px;
            padding: 1.75rem 1.35rem;
            box-shadow: 0 6px 20px -3px rgba(23, 32, 42, 0.04);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        .lux-feature-card:hover {
            transform: translateY(-6px);
            border-color: var(--c-gold);
            box-shadow: 0 18px 40px -8px rgba(217, 179, 108, 0.25);
        }

        .lux-feature-head {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 0.85rem;
        }

        .lux-feature-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(217, 179, 108, 0.15);
            color: var(--c-gold-dark);
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            flex: none;
        }

        .lux-feature-head h4 {
            font-family: var(--font-sans);
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0;
            line-height: 1.3;
        }

        .lux-feature-card p {
            color: var(--c-text-muted);
            font-size: 0.86rem;
            line-height: 1.6;
            margin: 0;
        }

        /* 4.6 Alur Supervisi */
        .lux-workflow-section {
            padding: 4.5rem 2rem;
            max-width: 1240px;
            margin: 0 auto;
            text-align: center;
        }

        .lux-workflow-header {
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .lux-workflow-header h2 {
            font-family: var(--font-serif);
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0.4rem 0 0.5rem;
        }

        .lux-workflow-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
        }

        .lux-workflow-item {
            background: #ffffff;
            border: 1px solid var(--c-border-light);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            text-align: center;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(23, 32, 42, 0.03);
        }

        .lux-workflow-item:hover {
            transform: translateY(-4px);
            border-color: var(--c-gold);
            box-shadow: 0 10px 25px rgba(217, 179, 108, 0.2);
        }

        .lux-step-num {
            font-family: var(--font-serif);
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--c-gold-dark);
            display: block;
            margin-bottom: 0.4rem;
        }

        .lux-workflow-item h5 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0;
            line-height: 1.35;
        }

        /* 4.7 Manfaat */
        .lux-benefits-section {
            padding: 2rem 2rem 6.5rem;
            max-width: 1240px;
            margin: 0 auto;
        }

        .lux-benefits-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .lux-benefit-card {
            background: #ffffff;
            border: 1px solid var(--c-border-light);
            border-radius: 16px;
            padding: 1.75rem 1.35rem;
            text-align: left;
            box-shadow: 0 4px 14px rgba(23, 32, 42, 0.03);
            transition: all 0.25s ease;
        }

        .lux-benefit-card:hover {
            transform: translateY(-4px);
            border-color: var(--c-gold);
        }

        .lux-benefit-num {
            font-family: var(--font-serif);
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--c-gold-dark);
            margin-bottom: 0.6rem;
            display: block;
            line-height: 1;
        }

        .lux-benefit-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--c-text-dark);
            margin: 0 0 0.5rem;
        }

        .lux-benefit-card p {
            color: var(--c-text-muted);
            font-size: 0.86rem;
            line-height: 1.6;
            margin: 0;
        }

        /* 4.8 Quote / CTA Banner (Dark Cinematic) */
        .lux-banner-cta {
            background: radial-gradient(800px 350px at 50% 0%, rgba(217, 179, 108, 0.16), transparent 70%),
                        linear-gradient(180deg, #0B1117 0%, #172331 100%);
            color: #ffffff;
            padding: 5.5rem 2rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .lux-banner-inner {
            max-width: 680px;
            margin: 0 auto;
        }

        .lux-banner-inner h2 {
            font-family: var(--font-serif);
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 700;
            margin-bottom: 1.85rem;
            line-height: 1.25;
        }

        /* 4.9 Footer */
        .lux-footer {
            background: #070B0E;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding: 3.5rem 2rem 2.5rem;
            font-size: 0.88rem;
        }

        .lux-footer-inner {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2.5rem;
        }

        .lux-footer-brand p {
            color: #64748b;
            line-height: 1.6;
            margin-top: 0.85rem;
            max-width: 320px;
        }

        .lux-footer-col h5 {
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1.15rem;
        }

        .lux-footer-col a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 0.6rem;
            transition: color 0.15s ease;
        }

        .lux-footer-col a:hover {
            color: var(--c-gold);
        }

        .lux-footer-strip {
            max-width: 1240px;
            margin: 0 auto;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            color: #64748b;
            font-size: 0.82rem;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .lux-about-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            .lux-features-grid-4,
            .lux-benefits-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .lux-workflow-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 640px) {
            .lux-nav-links {
                display: none;
            }
            .lux-features-grid-4,
            .lux-benefits-grid,
            .lux-workflow-grid {
                grid-template-columns: 1fr;
            }
            .lux-footer-inner {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
</head>
<body>

<?php
$ident   = $identitas ?? [];
$nama    = $namaSekolah ?? ($ident['nama_sekolah'] ?? 'Supervisi Guru');
$logo    = $ident['logo_url'] ?? base_url('assets/img/logo-placeholder.svg');
?>

<!-- 4.1 Navbar -->
<header class="lux-navbar">
    <div class="lux-nav-container">
        <a class="lux-brand" href="<?= base_url('/') ?>">
            <img src="<?= esc($logo, 'attr') ?>"
                 onerror="this.onerror=null;this.src='<?= base_url('assets/img/logo-placeholder.svg') ?>'"
                 alt="Logo <?= esc($nama, 'attr') ?>"
                 class="lux-brand-logo">
            <div class="lux-brand-text">
                <strong><?= esc($nama) ?></strong>
                <small>Bersama Mewujudkan Pendidikan Berkualitas</small>
            </div>
        </a>

        <nav class="lux-nav-links">
            <a href="#beranda" class="active">Beranda</a>
            <a href="#tentang">Tentang</a>
            <a href="#fitur">Fitur</a>
            <a href="#alur">Alur</a>
            <a href="#manfaat">Manfaat</a>
            <a href="#kontak">Kontak</a>
        </nav>

        <a href="<?= base_url('auth/login') ?>" class="lux-btn-nav">
            <i class="fas fa-sign-in-alt mr-1"></i> Masuk
        </a>
    </div>
</header>

<main>
    <!-- 4.2 Hero Section: WIDE BACKGROUND IMAGE + TRANSPARENT GLASS LAYER -->
    <section class="lux-hero-wide" id="beranda">
        <div class="lux-hero-content-wide">
            <!-- Eyebrow Pill -->
            <div class="lux-hero-glass-pill">
                <i class="fas fa-sparkles text-warning mr-1"></i> PLATFORM SUPERVISI GURU TERINTEGRASI
            </div>

            <!-- Grand Serif Headline -->
            <h1 class="lux-hero-h1-wide">
                Kolaborasi untuk <span class="gold-accent">Guru Hebat</span> dan Pendidikan yang <span class="gold-accent">Lebih Baik</span>
            </h1>

            <!-- Subtitle -->
            <p class="lux-hero-p-wide">
                Sistem supervisi guru yang modern, transparan, dan terukur untuk mendukung peningkatan kompetensi serta mutu pembelajaran di sekolah.
            </p>

            <!-- CTA Actions -->
            <div class="lux-hero-actions-wide">
                <a href="<?= base_url('auth/login') ?>" class="lux-btn-gold-large">
                    <span>Mulai Sekarang</span>
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
                <a href="#tentang" class="lux-btn-glass-large">
                    <span>Pelajari Lebih Lanjut</span>
                </a>
            </div>

            <!-- 3 Trust Indicators (Pills on Glass) -->
            <div class="lux-trust-strip-glass">
                <div class="lux-glass-badge">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Transparan dan Akuntabel</span>
                </div>
                <div class="lux-glass-badge">
                    <i class="fas fa-laptop"></i>
                    <span>Mudah Digunakan</span>
                </div>
                <div class="lux-glass-badge">
                    <i class="fas fa-chart-line"></i>
                    <span>Mendukung Pengembangan Guru</span>
                </div>
            </div>

            <!-- Floating Quote Bar -->
            <div class="lux-hero-quote-bar">
                “Guru yang terus belajar, akan selalu <span>menyalakan harapan</span>.”
            </div>
        </div>
    </section>

    <!-- Warm Ivory Surface: Tentang, Fitur, Alur, Manfaat -->
    <div class="lux-ivory-surface">
        
        <!-- 4.4 About Section -->
        <section class="lux-about-section" id="tentang">
            <div class="lux-about-grid">
                <div class="lux-about-left">
                    <span class="lux-sec-eyebrow">&mdash; TENTANG KAMI</span>
                    <h2>Mendukung Profesionalisme Guru Melalui Supervisi yang Bermakna</h2>
                    <p>
                        Web Supervisi Guru hadir sebagai solusi digital untuk mempermudah proses supervisi, pemantauan, dan tindak lanjut pembinaan guru. Kami percaya, guru yang didukung dengan baik akan melahirkan peserta didik yang luar biasa.
                    </p>

                    <div class="lux-quote-banner">
                        “Pendidikan yang berkualitas berawal dari guru yang berkualitas.”
                    </div>

                    <a href="<?= base_url('auth/login') ?>" class="lux-btn-link-gold">
                        <span>Selengkapnya</span>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="lux-about-visual">
                    <div class="lux-about-floating-badge">
                        <div class="badge-icon-small">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.84rem;color:var(--c-text-dark);display:block;">Bersama</strong>
                            <small style="color:var(--c-text-muted);">Membangun Generasi Masa Depan</small>
                        </div>
                    </div>

                    <div class="lux-about-photo-frame">
                        <img src="<?= base_url('assets/img/gedung-sekolah.jpg?v=' . time()) ?>" 
                             alt="Gedung Sekolah SMAN" 
                             loading="lazy"
                             onerror="this.onerror=null;this.src='<?= base_url('assets/img/campus-building.jpg') ?>'">
                    </div>
                </div>
            </div>
        </section>

        <!-- 4.5 Feature Section (4 Grid Cards) -->
        <section class="lux-features-section" id="fitur">
            <div class="lux-features-header">
                <small class="lux-sec-eyebrow">FITUR UNGGULAN</small>
                <h2>Solusi Lengkap untuk Supervisi Guru</h2>
                <p>Fitur dirancang untuk memudahkan setiap proses supervisi, dari perencanaan hingga evaluasi.</p>
            </div>

            <div class="lux-features-grid-4">
                <!-- Card 1 -->
                <div class="lux-feature-card">
                    <div class="lux-feature-head">
                        <div class="lux-feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h4>Perencanaan Supervisi</h4>
                    </div>
                    <p>Susun jadwal dan instrumen supervisi dengan mudah.</p>
                </div>

                <!-- Card 2 -->
                <div class="lux-feature-card">
                    <div class="lux-feature-head">
                        <div class="lux-feature-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h4>Pelaksanaan &amp; Observasi</h4>
                    </div>
                    <p>Catat hasil supervisi secara digital dan terstruktur.</p>
                </div>

                <!-- Card 3 -->
                <div class="lux-feature-card">
                    <div class="lux-feature-head">
                        <div class="lux-feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>Evaluasi &amp; Tindak Lanjut</h4>
                    </div>
                    <p>Pantau perkembangan dan rekomendasi pembinaan.</p>
                </div>

                <!-- Card 4 -->
                <div class="lux-feature-card">
                    <div class="lux-feature-head">
                        <div class="lux-feature-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <h4>Laporan Otomatis</h4>
                    </div>
                    <p>Hasil supervisi tersaji dalam laporan yang rapi dan siap unduh.</p>
                </div>
            </div>
        </section>

        <!-- 4.6 Alur Supervisi -->
        <section class="lux-workflow-section" id="alur">
            <div class="lux-workflow-header">
                <small class="lux-sec-eyebrow">ALUR KERJA</small>
                <h2>Tahapan Pelaksanaan Supervisi</h2>
                <p>Siklus supervisi terpadu untuk memastikan evaluasi berjalan objektif dan akuntabel.</p>
            </div>

            <div class="lux-workflow-grid">
                <div class="lux-workflow-item">
                    <span class="lux-step-num">01</span>
                    <h5>Perencanaan</h5>
                </div>
                <div class="lux-workflow-item">
                    <span class="lux-step-num">02</span>
                    <h5>Observasi</h5>
                </div>
                <div class="lux-workflow-item">
                    <span class="lux-step-num">03</span>
                    <h5>Penilaian</h5>
                </div>
                <div class="lux-workflow-item">
                    <span class="lux-step-num">04</span>
                    <h5>Evaluasi</h5>
                </div>
                <div class="lux-workflow-item">
                    <span class="lux-step-num">05</span>
                    <h5>Tindak Lanjut</h5>
                </div>
                <div class="lux-workflow-item">
                    <span class="lux-step-num">06</span>
                    <h5>Laporan</h5>
                </div>
            </div>
        </section>

        <!-- 4.7 Manfaat -->
        <section class="lux-benefits-section" id="manfaat">
            <div class="lux-features-header mb-4">
                <small class="lux-sec-eyebrow">MANFAAT SISTEM</small>
                <h2>Dampak Nyata bagi Mutu Pendidikan</h2>
                <p>Membantu kepemimpinan sekolah dan dewan guru bertumbuh bersama secara berkesinambungan.</p>
            </div>

            <div class="lux-benefits-grid">
                <div class="lux-benefit-card">
                    <span class="lux-benefit-num">01</span>
                    <h4>Perencanaan Terarah</h4>
                    <p>Supervisi lebih terstruktur dengan kalender akademik dan kelompok pembina yang jelas.</p>
                </div>
                <div class="lux-benefit-card">
                    <span class="lux-benefit-num">02</span>
                    <h4>Observasi Terstruktur</h4>
                    <p>Pengamatan pembelajaran berbasis instrumen standar tanpa prasangka personal.</p>
                </div>
                <div class="lux-benefit-card">
                    <span class="lux-benefit-num">03</span>
                    <h4>Evaluasi Berkelanjutan</h4>
                    <p>Rekomendasi tindak lanjut yang nyata untuk peningkatan pedagogik dan profesionalisme guru.</p>
                </div>
                <div class="lux-benefit-card">
                    <span class="lux-benefit-num">04</span>
                    <h4>Laporan Terintegrasi</h4>
                    <p>Keputusan berbasis data akurat dan arsip digital siap pakai untuk akreditasi sekolah.</p>
                </div>
            </div>
        </section>

    </div>

    <!-- 4.8 Quote / CTA Banner (Dark Cinematic) -->
    <section class="lux-banner-cta">
        <div class="lux-banner-inner">
            <h2>Satu langkah kecil untuk perubahan besar dalam dunia pendidikan.</h2>
            <a href="<?= base_url('auth/login') ?>" class="lux-btn-gold-large">
                <span>Mulai Menggunakan Supervisi Guru</span>
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </section>
</main>

<!-- 4.9 Footer -->
<footer class="lux-footer" id="kontak">
    <div class="lux-footer-inner">
        <div class="lux-footer-brand">
            <div class="lux-brand mb-2">
                <img src="<?= esc($logo, 'attr') ?>"
                     onerror="this.onerror=null;this.src='<?= base_url('assets/img/logo-placeholder.svg') ?>'"
                     alt="Logo <?= esc($nama, 'attr') ?>"
                     class="lux-brand-logo"
                     style="width:38px;height:38px;">
                <div class="lux-brand-text">
                    <strong style="font-size:1.05rem;"><?= esc($nama) ?></strong>
                    <small>Bersama Mewujudkan Pendidikan Berkualitas</small>
                </div>
            </div>
            <p>
                Platform digital untuk mendukung supervisi, pengembangan profesionalisme guru, dan peningkatan mutu pembelajaran.
            </p>
        </div>

        <div class="lux-footer-col">
            <h5>Navigasi</h5>
            <a href="#beranda">Beranda</a>
            <a href="#tentang">Tentang</a>
            <a href="#fitur">Fitur</a>
            <a href="#alur">Alur</a>
            <a href="#manfaat">Manfaat</a>
            <a href="<?= base_url('auth/login') ?>">Masuk Dashboard</a>
        </div>

        <div class="lux-footer-col">
            <h5>Kontak &amp; Informasi</h5>
            <p style="color:#94a3b8;font-size:0.86rem;margin-bottom:0.5rem;">
                <i class="fas fa-map-marker-alt text-warning mr-1"></i>
                <?= esc($ident['alamat'] ?? 'Indonesia') ?>
            </p>
            <p style="color:#94a3b8;font-size:0.86rem;margin-bottom:0.5rem;">
                <i class="fas fa-envelope text-warning mr-1"></i>
                <?= esc($ident['kontak'] ?? 'info@sekolah.sch.id') ?>
            </p>
            <?php if (!empty($ident['npsn'])): ?>
                <p style="color:#94a3b8;font-size:0.86rem;">
                    <i class="fas fa-certificate text-warning mr-1"></i> NPSN: <?= esc($ident['npsn']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <div class="lux-footer-strip">
        <span>Copyright &copy; TIM IT <?= esc($nama) ?> <?= date('Y') ?></span>
        <span>Integritas &bull; Kolaborasi &bull; Profesional &bull; Berkelanjutan</span>
    </div>
</footer>

</body>
</html>
