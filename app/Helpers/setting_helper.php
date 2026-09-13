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

if (!function_exists('get_nama_madrasah')) {
    /**
     * Get school name from system settings
     *
     * @return string
     */
    function get_nama_madrasah(): string
    {
        return get_pengaturan('nama_madrasah', 'MIN 2 TANGGAMUS');
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
        $namaMadrasah = get_nama_madrasah();
        $alamat = get_pengaturan('alamat', 'Jl. Lapangan Ampera Purwodadi No. 109');
        $kecamatan = get_pengaturan('kecamatan', 'Gisting');
        $kabupaten = get_pengaturan('kabupaten', 'Kabupaten Tanggamus');
        $provinsi = get_pengaturan('provinsi', 'Lampung');
        $email = get_pengaturan('email', 'min2tanggamus@kemenag.go.id');
        $telepon = get_pengaturan('telepon', '');

        $defaultAlamat = trim("{$alamat} Kec. {$kecamatan} {$kabupaten} - {$provinsi}", ' -');
        $defaultKontak = trim(($telepon ? "Telp: {$telepon} " : '') . ($email ? "Email: {$email}" : ''));

        return [
            'baris_1' => get_pengaturan('kop_baris_1', 'KEMENTERIAN AGAMA REPUBLIK INDONESIA'),
            'baris_2' => get_pengaturan('kop_baris_2', 'KANTOR KEMENTERIAN AGAMA KABUPATEN TANGGAMUS'),
            'baris_3' => get_pengaturan('kop_baris_3', strtoupper($namaMadrasah)),
            'baris_4' => get_pengaturan('kop_baris_4', $defaultAlamat),
            'baris_5' => get_pengaturan('kop_baris_5', $defaultKontak ?: 'Website: https://min2tanggamus.sch.id'),
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