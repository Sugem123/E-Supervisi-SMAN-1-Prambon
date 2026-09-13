<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class WaktuController extends BaseController
{
    public function index()
    {
        // Data untuk dikirim ke view
        $data = [
            'title' => 'Waktu Indonesia Barat (WIB)',
            'current_time' => date('Y-m-d H:i:s')
        ];
        
        return view('partials/waktu_wib', $data);
    }
    
    public function getCurrentTime()
    {
        // Mengembalikan waktu saat ini dalam format JSON
        $currentTime = date('Y-m-d H:i:s');
        
        return $this->response->setJSON([
            'current_time' => $currentTime,
            'formatted_time' => format_waktu_indonesia($currentTime),
            'formatted_datetime' => format_datetime_indonesia($currentTime)
        ]);
    }
}