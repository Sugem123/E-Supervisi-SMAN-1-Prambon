<?php

if (!function_exists('get_pengaturan')) {
    /**
     * Get system setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_pengaturan(string $key, $default = null)
    {
        try {
            $settingModel = new \App\Models\SystemSettingModel();
            $val = $settingModel->getSetting($key, $default);
            return ($val !== null && $val !== '') ? $val : $default;
        } catch (\Throwable $e) {
            return $default;
        }
    }
}

if (!function_exists('get_nama_sekolah')) {
    /**
     * Get school name from system settings (SMA).
     * Backward-compatible: falls back to legacy 'nama_madrasah' key.
     *
     * @return string
     */
    function get_nama_sekolah(): string
    {
        $nama = get_pengaturan('nama_sekolah', null);
        if ($nama !== null && $nama !== '') {
            return $nama;
        }
        return get_pengaturan('nama_madrasah', 'SMA NEGERI 1 CONTOH');
    }
}

if (!function_exists('get_nama_madrasah')) {
    /**
     * Legacy wrapper kept for backward compatibility.
     *
     * @return string
     */
    function get_nama_madrasah(): string
    {
        return get_nama_sekolah();
    }
}

if (!function_exists('get_logo_url')) {
    /**
     * URL logo publik dengan fallback placeholder premium.
     * Bisa diatur via Admin > Pengaturan > Identitas Sekolah.
     *
     * @param string $key logo|sidebar_logo|kop_logo_kiri
     */
    function get_logo_url(string $key = 'logo', ?string $fallback = null): string
    {
        $fallback = $fallback ?? 'assets/img/logo-placeholder.svg';
        $filename = get_pengaturan($key, '');
        if (is_string($filename) && $filename !== '') {
            $candidates = [
                FCPATH . 'uploads/' . $filename,
                ROOTPATH . 'public/uploads/' . $filename,
            ];
            foreach ($candidates as $path) {
                if ($path !== '' && is_file($path)) {
                    return base_url('uploads/' . $filename);
                }
            }
        }

        return base_url($fallback);
    }
}

if (!function_exists('has_custom_logo')) {
    function has_custom_logo(string $key = 'logo'): bool
    {
        $filename = get_pengaturan($key, '');
        if (!is_string($filename) || $filename === '') {
            return false;
        }
        return is_file(FCPATH . 'uploads/' . $filename) || is_file(ROOTPATH . 'public/uploads/' . $filename);
    }
}

if (!function_exists('get_identitas_publik')) {
    /**
     * Identitas publik untuk landing/login dengan placeholder elegan.
     * Semua nilai bisa diatur via Admin > Pengaturan > Identitas Sekolah.
     */
    function get_identitas_publik(): array
    {
        $nama = get_nama_sekolah();
        $alamat = get_pengaturan('alamat', '');
        $kecamatan = get_pengaturan('kecamatan', '');
        $kabupaten = get_pengaturan('kabupaten', '');
        $provinsi = get_pengaturan('provinsi', '');
        $alamatLengkap = trim(implode(', ', array_filter([$alamat, $kecamatan, $kabupaten, $provinsi])));
        $telepon = get_pengaturan('telepon', '');
        $email = get_pengaturan('email', '');
        $kontak = trim(implode(' · ', array_filter([$telepon, $email])));

        return [
            'nama_sekolah'     => $nama !== '' ? $nama : 'Nama Sekolah Belum Diatur',
            'nama_sekolah_raw' => $nama,
            'npsn'             => get_pengaturan('npsn', '') !== '' ? get_pengaturan('npsn', '') : 'NPSN belum diatur',
            'alamat'           => $alamatLengkap !== '' ? $alamatLengkap : 'Alamat sekolah belum diatur via Pengaturan',
            'telepon'          => $telepon !== '' ? $telepon : '',
            'email'            => $email !== '' ? $email : '',
            'kontak'           => $kontak !== '' ? $kontak : 'Kontak belum diatur',
            'nama_kepala'      => get_pengaturan('nama_kepala', '') !== '' ? get_pengaturan('nama_kepala', '') : 'Kepala Sekolah belum diatur',
            'logo_url'         => get_logo_url('logo'),
            'sidebar_logo_url' => get_logo_url('sidebar_logo'),
            'has_logo'         => has_custom_logo('logo'),
            'has_sidebar_logo' => has_custom_logo('sidebar_logo'),
        ];
    }
}

if (!function_exists('get_nama_kepala')) {
    /**
     * Get headmaster name from system settings
     *
     * @return string
     */
    function get_nama_kepala(): string
    {
        return get_pengaturan('nama_kepala', 'Kepala Sekolah');
    }
}

if (!function_exists('get_kop_logo_src')) {
    /**
     * Helper to get logo source suitable for Dompdf (base64 or URL)
     *
     * @param string|null $filename
     * @return string|null
     */
    function get_kop_logo_src(?string $filename): ?string
    {
        if (empty($filename)) {
            return null;
        }

        // Check multiple potential file locations (local dev, cPanel public_html, etc.)
        $possiblePaths = [
            FCPATH . 'uploads/' . $filename,
            ROOTPATH . 'public/uploads/' . $filename,
            ROOTPATH . 'public_html/uploads/' . $filename,
            rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filename,
            WRITEPATH . 'uploads/' . $filename
        ];

        foreach ($possiblePaths as $filepath) {
            if (!empty($filepath) && file_exists($filepath) && is_file($filepath)) {
                $mime = function_exists('mime_content_type') ? (mime_content_type($filepath) ?: 'image/png') : 'image/png';
                $data = base64_encode(file_get_contents($filepath));
                return 'data:' . $mime . ';base64,' . $data;
            }
        }

        // Only fallback to URL if allow_url_fopen is enabled on server
        if (ini_get('allow_url_fopen')) {
            return base_url('uploads/' . $filename);
        }

        // Return null instead of broken URL so Dompdf does not crash on hosting
        return null;
    }
}

if (!function_exists('get_kop_data')) {
    /**
     * Get kop surat settings array with fallback defaults
     *
     * @return array
     */
    function get_kop_data(): array
    {
        $namaMadrasah = get_nama_sekolah();
        $alamat = get_pengaturan('alamat', 'Jl. Pendidikan No. 1');
        $kecamatan = get_pengaturan('kecamatan', 'Kecamatan Contoh');
        $kabupaten = get_pengaturan('kabupaten', 'Kabupaten Contoh');
        $provinsi = get_pengaturan('provinsi', 'Provinsi Contoh');
        $email = get_pengaturan('email', 'info@sman1contoh.sch.id');
        $telepon = get_pengaturan('telepon', '');

        $defaultAlamat = trim("{$alamat} Kec. {$kecamatan} {$kabupaten} - {$provinsi}", ' -');
        $defaultKontak = trim(($telepon ? "Telp: {$telepon} " : '') . ($email ? "Email: {$email}" : ''));

        return [
            'baris_1' => get_pengaturan('kop_baris_1', 'PEMERINTAH PROVINSI CONTOH'),
            'baris_2' => get_pengaturan('kop_baris_2', 'DINAS PENDIDIKAN'),
            'baris_3' => get_pengaturan('kop_baris_3', strtoupper($namaMadrasah)),
            'baris_4' => get_pengaturan('kop_baris_4', $defaultAlamat),
            'baris_5' => get_pengaturan('kop_baris_5', $defaultKontak ?: 'Website: https://sman1contoh.sch.id'),
            'logo_kiri' => get_pengaturan('kop_logo_kiri', get_pengaturan('logo', '')),
            'logo_kanan' => get_pengaturan('kop_logo_kanan', ''),
            'tampilkan_logo' => get_pengaturan('kop_tampilkan_logo', '1'),
            'tampilkan_garis' => get_pengaturan('kop_tampilkan_garis', '1'),
        ];
    }
}

if (!function_exists('render_kop_surat')) {
    /**
     * Render HTML Kop Instansi / Kop Surat for PDF generation
     *
     * @param array $customData
     * @return string
     */
    function render_kop_surat(array $customData = []): string
    {
        $kop = array_merge(get_kop_data(), $customData);

        $logoKiriSrc = !empty($kop['logo_kiri']) ? get_kop_logo_src($kop['logo_kiri']) : null;
        $logoKananSrc = !empty($kop['logo_kanan']) ? get_kop_logo_src($kop['logo_kanan']) : null;
        $showLogo = ($kop['tampilkan_logo'] === '1' || $kop['tampilkan_logo'] === true || $kop['tampilkan_logo'] === 1);
        $showGaris = ($kop['tampilkan_garis'] === '1' || $kop['tampilkan_garis'] === true || $kop['tampilkan_garis'] === 1);

        $html = '<style>.kop-surat-table, .kop-surat-table tr, .kop-surat-table td { border: 0px none transparent !important; border-style: none !important; border-width: 0 !important; background: transparent !important; }</style>';
        $html .= '<table class="kop-surat-table" style="width: 100%; border: 0px none transparent !important; border-collapse: collapse !important; margin-bottom: 2px; font-family: Arial, Helvetica, sans-serif; background: transparent !important;">';
        $html .= '<tr style="border: 0px none transparent !important; background: transparent !important;">';

        // Logo Kiri
        if ($showLogo && $logoKiriSrc) {
            $html .= '<td style="width: 75px; text-align: center; vertical-align: middle; padding-right: 8px; border: 0px none transparent !important; background: transparent !important;">';
            $html .= '<img src="' . $logoKiriSrc . '" style="max-width: 70px; max-height: 70px; height: auto; border: none !important;" alt="Logo">';
            $html .= '</td>';
        }

        // Teks Kop Tengah
        $html .= '<td style="text-align: center; vertical-align: middle; padding: 0 5px; border: 0px none transparent !important; background: transparent !important;">';
        if (!empty($kop['baris_1'])) {
            $html .= '<div style="font-size: 11pt; font-weight: bold; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.5px;">' . esc($kop['baris_1']) . '</div>';
        }
        if (!empty($kop['baris_2'])) {
            $html .= '<div style="font-size: 12pt; font-weight: bold; line-height: 1.2; text-transform: uppercase; letter-spacing: 0.5px;">' . esc($kop['baris_2']) . '</div>';
        }
        if (!empty($kop['baris_3'])) {
            $html .= '<div style="font-size: 13pt; font-weight: bold; line-height: 1.2; text-transform: uppercase; margin: 2px 0;">' . esc($kop['baris_3']) . '</div>';
        }
        if (!empty($kop['baris_4'])) {
            $html .= '<div style="font-size: 9pt; line-height: 1.3;">' . esc($kop['baris_4']) . '</div>';
        }
        if (!empty($kop['baris_5'])) {
            $html .= '<div style="font-size: 8.5pt; line-height: 1.3; font-style: italic; color: #222;">' . esc($kop['baris_5']) . '</div>';
        }
        $html .= '</td>';

        // Logo Kanan (opsional)
        if ($showLogo && $logoKananSrc) {
            $html .= '<td style="width: 75px; text-align: center; vertical-align: middle; padding-left: 8px; border: 0px none transparent !important; background: transparent !important;">';
            $html .= '<img src="' . $logoKananSrc . '" style="max-width: 70px; max-height: 70px; height: auto; border: none !important;" alt="Logo">';
            $html .= '</td>';
        }

        $html .= '</tr>';
        $html .= '</table>';

        // Garis Pemisah Kop Ganda (Tebal di atas, Tipis di bawah - standar resmi kop surat Indonesia)
        if ($showGaris) {
            $html .= '<div style="border-top: 2px solid #000; border-bottom: 1px solid #000; height: 2px; margin: 3px 0 15px 0;"></div>';
        }

        return $html;
    }
}

if (!function_exists('get_default_jam_pelajaran')) {
    /**
     * Jadwal Jam Pelajaran Default Standar SMA (durasi 45 menit/JP, 10 Jam Pelajaran + 2 Istirahat).
     */
    function get_default_jam_pelajaran(): array
    {
        return [
            ['jam_ke' => '1', 'waktu_dari' => '07:00', 'waktu_sampai' => '07:45', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-1 (KBM)'],
            ['jam_ke' => '2', 'waktu_dari' => '07:45', 'waktu_sampai' => '08:30', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-2 (KBM)'],
            ['jam_ke' => '3', 'waktu_dari' => '08:30', 'waktu_sampai' => '09:15', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-3 (KBM)'],
            ['jam_ke' => '4', 'waktu_dari' => '09:15', 'waktu_sampai' => '10:00', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-4 (KBM)'],
            ['jam_ke' => '-', 'waktu_dari' => '10:00', 'waktu_sampai' => '10:30', 'is_istirahat' => 1, 'keterangan' => 'Istirahat I'],
            ['jam_ke' => '5', 'waktu_dari' => '10:30', 'waktu_sampai' => '11:15', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-5 (KBM)'],
            ['jam_ke' => '6', 'waktu_dari' => '11:15', 'waktu_sampai' => '12:00', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-6 (KBM)'],
            ['jam_ke' => '-', 'waktu_dari' => '12:00', 'waktu_sampai' => '12:45', 'is_istirahat' => 1, 'keterangan' => 'Istirahat II / Sholat Dhuhur'],
            ['jam_ke' => '7', 'waktu_dari' => '12:45', 'waktu_sampai' => '13:30', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-7 (KBM)'],
            ['jam_ke' => '8', 'waktu_dari' => '13:30', 'waktu_sampai' => '14:15', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-8 (KBM)'],
            ['jam_ke' => '9', 'waktu_dari' => '14:15', 'waktu_sampai' => '15:00', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-9 (KBM)'],
            ['jam_ke' => '10', 'waktu_dari' => '15:00', 'waktu_sampai' => '15:45', 'is_istirahat' => 0, 'keterangan' => 'Jam Ke-10 (KBM)'],
        ];
    }
}

if (!function_exists('get_jam_pelajaran')) {
    /**
     * Mengambil daftar konfigurasi jam pelajaran (termasuk istirahat) dari Pengaturan Sistem.
     */
    function get_jam_pelajaran(): array
    {
        $raw = get_pengaturan('jam_pelajaran', null);
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }
        return get_default_jam_pelajaran();
    }
}

if (!function_exists('get_jam_pelajaran_kbm')) {
    /**
     * Mengambil slot jam pelajaran KBM aktif (tanpa istirahat) untuk alokasi jadwal supervisi.
     */
    function get_jam_pelajaran_kbm(): array
    {
        $all = get_jam_pelajaran();
        $kbm = [];
        foreach ($all as $item) {
            if (empty($item['is_istirahat']) && !empty($item['jam_ke']) && $item['jam_ke'] !== '-') {
                $jk = (string)$item['jam_ke'];
                $dari = substr($item['waktu_dari'] ?? '07:00', 0, 5);
                $sampai = substr($item['waktu_sampai'] ?? '07:45', 0, 5);
                $kbm[$jk] = [
                    'jam_ke'       => $jk,
                    'waktu_dari'   => $dari,
                    'waktu_sampai' => $sampai,
                    'keterangan'   => $item['keterangan'] ?? "Jam Ke-{$jk}",
                    'label'        => "Jam Ke-{$jk} ({$dari} - {$sampai})"
                ];
            }
        }
        if (empty($kbm)) {
            foreach (get_default_jam_pelajaran() as $item) {
                if (empty($item['is_istirahat']) && !empty($item['jam_ke']) && $item['jam_ke'] !== '-') {
                    $jk = (string)$item['jam_ke'];
                    $dari = substr($item['waktu_dari'], 0, 5);
                    $sampai = substr($item['waktu_sampai'], 0, 5);
                    $kbm[$jk] = [
                        'jam_ke'       => $jk,
                        'waktu_dari'   => $dari,
                        'waktu_sampai' => $sampai,
                        'keterangan'   => $item['keterangan'] ?? "Jam Ke-{$jk}",
                        'label'        => "Jam Ke-{$jk} ({$dari} - {$sampai})"
                    ];
                }
            }
        }
        return $kbm;
    }
}