<?php

/**
 * Date Helper untuk format tanggal dalam bahasa Indonesia
 * 
 * Helper ini menyediakan fungsi untuk memformat tanggal dalam bahasa Indonesia
 * dengan format yang mudah dibaca.
 * 
 * Cara penggunaan:
 * 1. Pastikan helper ini diload di Config/Autoload.php
 * 2. Gunakan fungsi format_tanggal_indonesia($date_string, $show_day)
 * 3. Gunakan fungsi format_hari_indonesia($date_string)
 */

if (!function_exists('format_tanggal_indonesia')) {
    /**
     * Mengubah format tanggal menjadi format Indonesia
     * Contoh: Senin, 1 Januari 2025
     * 
     * @param string $date_string Tanggal dalam format Y-m-d atau timestamp
     * @param bool $show_day Menampilkan nama hari (default: true)
     * @return string Tanggal dalam format Indonesia
     */
    function format_tanggal_indonesia($date_string, $show_day = true)
    {
        if (empty($date_string)) {
            return '';
        }
        
        // Parsing tanggal
        $timestamp = strtotime($date_string);
        if ($timestamp === false) {
            return $date_string; // Return original jika tidak valid
        }
        
        // Array nama hari dalam bahasa Indonesia
        $hari = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 
            'Kamis', 'Jumat', 'Sabtu'
        ];
        
        // Array nama bulan dalam bahasa Indonesia
        $bulan = [
            '', // Placeholder untuk index 0
            'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];
        
        // Mendapatkan bagian-bagian tanggal
        $day_of_week = date('w', $timestamp);
        $day = date('j', $timestamp);
        $month = date('n', $timestamp);
        $year = date('Y', $timestamp);
        
        // Format tanggal
        $formatted_date = '';
        if ($show_day) {
            $formatted_date .= $hari[$day_of_week] . ', ';
        }
        
        $formatted_date .= $day . ' ' . $bulan[$month] . ' ' . $year;
        
        return $formatted_date;
    }
}

if (!function_exists('format_hari_indonesia')) {
    /**
     * Mendapatkan nama hari dalam bahasa Indonesia
     * 
     * @param string $date_string Tanggal dalam format Y-m-d atau timestamp
     * @return string Nama hari dalam bahasa Indonesia
     */
    function format_hari_indonesia($date_string)
    {
        if (empty($date_string)) {
            return '';
        }
        
        // Parsing tanggal
        $timestamp = strtotime($date_string);
        if ($timestamp === false) {
            return ''; // Return kosong jika tidak valid
        }
        
        // Array nama hari dalam bahasa Indonesia
        $hari = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 
            'Kamis', 'Jumat', 'Sabtu'
        ];
        
        // Mendapatkan hari
        $day_of_week = date('w', $timestamp);
        
        return $hari[$day_of_week];
    }
}

if (!function_exists('format_waktu_indonesia')) {
    /**
     * Menampilkan waktu dalam format WIB (Waktu Indonesia Barat)
     * 
     * @param string $datetime_string Tanggal dan waktu dalam format Y-m-d H:i:s atau timestamp
     * @param bool $show_timezone Menampilkan zona waktu (default: true)
     * @return string Waktu dalam format Indonesia dengan zona WIB
     */
    function format_waktu_indonesia($datetime_string, $show_timezone = true)
    {
        if (empty($datetime_string)) {
            return '';
        }
        
        // Parsing tanggal dan waktu
        $timestamp = strtotime($datetime_string);
        if ($timestamp === false) {
            return $datetime_string; // Return original jika tidak valid
        }
        
        // Format waktu sesuai zona waktu aplikasi
        $formatted_time = date('H:i:s', $timestamp);
        
        // Tambahkan zona waktu jika diminta
        if ($show_timezone) {
            $formatted_time .= ' WIB';
        }
        
        return $formatted_time;
    }
}

if (!function_exists('format_datetime_indonesia')) {
    /**
     * Menampilkan tanggal dan waktu lengkap dalam format Indonesia dengan zona WIB
     * 
     * @param string $datetime_string Tanggal dan waktu dalam format Y-m-d H:i:s atau timestamp
     * @param bool $show_day Menampilkan nama hari (default: true)
     * @param bool $show_timezone Menampilkan zona waktu (default: true)
     * @return string Tanggal dan waktu dalam format Indonesia dengan zona WIB
     */
    function format_datetime_indonesia($datetime_string, $show_day = true, $show_timezone = true)
    {
        if (empty($datetime_string)) {
            return '';
        }
        
        // Parsing tanggal dan waktu
        $timestamp = strtotime($datetime_string);
        if ($timestamp === false) {
            return $datetime_string; // Return original jika tidak valid
        }
        
        // Format tanggal
        $formatted_date = format_tanggal_indonesia(date('Y-m-d', $timestamp), $show_day);
        
        // Format waktu
        $formatted_time = date('H:i:s', $timestamp);
        
        // Gabungkan tanggal dan waktu
        $formatted_datetime = $formatted_date . ' ' . $formatted_time;
        
        // Tambahkan zona waktu jika diminta
        if ($show_timezone) {
            $formatted_datetime .= ' WIB';
        }
        
        return $formatted_datetime;
    }
}