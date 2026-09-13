<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Supervisi System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Font (Opsional, tapi mirip dengan gambar) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            height: 100%;
            margin: 0;
            padding: 1rem;
            font-family: 'Poppins', sans-serif;
            /* Font baru yang lebih bulat */

            /* 1. Latar Belakang (WAJIB untuk glassmorphism) */
            /* Latar belakang yang Anda pilih */
            background-image: url('https://images.pexels.com/photos/32612753/pexels-photo-32612753.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            /* Agar background diam */

            /* Penengah konten */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 2. Kartu Kaca (Efek Glassmorphism) */
        .glass-container {
            width: 100%;
            max-width: 420px;
            /* Lebar form yang ideal */
            padding: 2.5rem;
            color: white;
            /* Ubah warna teks default jadi putih */

            /* --- EFEK KACA UTAMA --- */
            background: rgba(255, 255, 255, 0.1);
            /* 1. Transparansi */
            backdrop-filter: blur(10px);
            /* 2. Blur (Inti) */
            -webkit-backdrop-filter: blur(10px);
            /* 3. Support Safari */
            border: 1px solid rgba(255, 255, 255, 0.18);
            /* 4. Border tipis */
            /* ------------------------- */

            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-container h1 {
            font-weight: 600;
            /* Lebih tebal */
        }

        /* 3. Grup Input Kustom */
        .form-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        /* 4. Input Kustom (bukan form-floating) */
        .glass-input {
            background: rgba(255, 255, 255, 0.15) !important;
            /* Latar input transparan */
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 10px !important;
            color: white !important;
            /* Warna teks saat mengetik */
            padding-left: 3rem !important;
            /* Beri ruang untuk ikon kiri */
        }

        /* Styling Placeholder */
        .glass-input::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Styling saat di-klik (focus) */
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.2) !important;
            box-shadow: none !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
        }

        /* 5. Ikon di dalam input (kiri) */
        .input-icon-left {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            z-index: 10;
        }

        /* 6. Ikon toggle password (kanan) - Modifikasi dari kode Anda */
        .password-toggle {
            position: absolute;
            right: 1rem;
            /* Ubah dari 15px */
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            /* Warna ikon mata */
            z-index: 10;
        }

        .password-toggle:hover {
            color: white;
        }

        /* 7. Tombol Login (Putih Solid) */
        .btn-login {
            background-color: #ffffff;
            border-color: #ffffff;
            color: #333;
            /* Warna teks di tombol */
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 50px;
            /* Buat jadi "pil" */
            width: 100%;
        }

        .btn-login:hover {
            background-color: #164227;
            border-color: #0ef40aff;
            color: #ffffffff;

        }

        /* 8. Link Ekstra */
        .form-check-label,
        .forgot-link {
            font-size: 0.9em;
            color: rgba(255, 255, 255, 0.9);
        }

        .forgot-link {
            text-decoration: none;
        }

        .forgot-link:hover {
            color: white;
            text-decoration: underline;
        }

        .register-link {
            font-size: 0.9em;
            color: rgba(255, 255, 255, 0.9);
        }

        .register-link a {
            color: white;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Styling untuk 'Remember me' agar warnanya putih */
        .form-check-input:checked {
            background-color: #ffffff;
            border-color: #ffffff;
        }

        .form-check-input {
            background-color: transparent;
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Styling untuk Alert Error PHP */
        .alert {
            /* Pastikan alert juga sedikit transparan */
            background-color: rgba(248, 215, 218, 0.8);
            border-color: rgba(245, 198, 203, 0.8);
            color: #721c24;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.25);
                /* Dibuat sedikit lebih besar */
                opacity: 0.9;
            }
        }

        .heart-pulse {
            /* Menerapkan animasi */
            animation: pulse 1.2s ease-in-out infinite;
            /* Sedikit dipercepat */
            display: inline-block;
            /* Warna hati (Tailwind red-500) */
            color: #ff0000ff;
        }
    </style>
</head>

<body>

    <div class="glass-container">
        <h1 class="text-center fw-bold mb-4">SISTEM SUPERVISI</h1>
        <h3 class="text-center fw-bold mb-4 text-secondary display-5 justify-content-center text-success">MIN 2 <br>TANGGAMUS</h3>
        <form action="<?= base_url('auth/attemptLogin') ?>" method="post">
            <?= csrf_field() ?>

            <!-- PHP Error/Validation Blocks (Tidak diubah) -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>
            <!-- Akhir PHP Blocks -->

            <!-- Input Email/Username (Struktur HTML diubah) -->
            <div class="form-group-custom">
                <i class="bi bi-person-circle input-icon-left text-dark "></i>
                <input type="email" class="form-control glass-input" id="email" name="email" placeholder="Username or Email" value="<?= old('email') ?>" required>
            </div>

            <!-- Input Password (Struktur HTML diubah) -->
            <div class="form-group-custom">
                <i class="bi bi-lock-fill input-icon-left text-dark"></i>
                <input type="password" class="form-control glass-input" id="password" name="password" placeholder="Password" required>
                <!-- Ikon Toggle Password (dari kode Anda) -->
                <i class="bi bi-eye password-toggle text-success" id="togglePassword"></i>
            </div>

            <!-- Checkbox & Lupa Password (Baru) -->
            <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="remember-me" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">
                        Remember me
                    </LAbel>
                </div>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div> -->

            <!-- Tombol Sign in (Kelas diubah) -->
            <button class="btn btn-login" type="submit">Login</button>

            <!-- Copyright (dipindah ke bawah) -->

            <footer class="w-full border-t border-gray-200 shadow-sm p-6 text-dark mt-4">
                <div class="max-w-7xl mx-auto text-center">
                    <!-- Baris utama footer -->
                    <p class="text-sm text-gray-600">
                        &copy; 2025 Dibuat dengan <span class="heart-pulse" role="status" aria-hidden="true">♥️</span> <br>di MIN 2 Tanggamus
                    </p>
                    <!-- Baris versi (dibuat lebih halus) -->
                    <p class="mt-2 text-xs text-gray-400 tracking-wide">
                        VERSI 1.0 BETA TEST
                    </p>
                </div>
            </footer>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Toggle Password (Tidak diubah, ini sudah benar) -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            // Toggle tipe input
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Toggle ikon mata
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>