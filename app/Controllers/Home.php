<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        helper('setting');

        // Landing publik: pengunjung belum login melihat halaman utama premium.
        // Pengguna yang sudah login diarahkan ke dashboard sesuai role.
        if (!session()->get('logged_in')) {
            $db = \Config\Database::connect();
            $stats = ['guru' => 0, 'jadwal' => 0, 'selesai' => 0, 'tahun' => null];
            try {
                $stats['guru'] = (int) $db->table('guru')->countAllResults();
                $stats['jadwal'] = (int) $db->table('jadwal_supervisi')->countAllResults();
                $stats['selesai'] = (int) $db->table('jadwal_supervisi')->where('status', 'Selesai')->countAllResults();
                $tahun = $db->table('tahun_ajar')->where('status_aktif', 'Aktif')->orderBy('id', 'DESC')->get()->getRowArray();
                $stats['tahun'] = $tahun ?: null;
            } catch (\Throwable $e) {
                // Landing tetap tampil walau statistik gagal dibaca.
            }

            return view('landing/index', [
                'namaSekolah' => get_nama_sekolah(),
                'identitas'   => get_identitas_publik(),
                'stats'       => $stats,
            ]);
        }

        // Redirect to dashboard which will redirect based on user role
        return redirect()->to('/dashboard');
    }
}