<?php

if (!function_exists('calculate_teacher_average_score')) {
    /**
     * Menghitung rata-rata nilai supervisi untuk seorang guru
     * 
     * @param int $guruId ID guru
     * @param object $jadwalModel Instance dari JadwalSupervisiModel
     * @param int|null $tahunAjarId ID tahun ajaran (opsional)
     * @return array Informasi rata-rata nilai dan jumlah supervisi
     */
    function calculate_teacher_average_score($guruId, $jadwalModel, $tahunAjarId = null) {
        // Load required models
        $hasilModel = new \App\Models\HasilSupervisiModel();
        $detailModel = new \App\Models\DetailHasilPenilaianModel();
        
        $schedulesQuery = $jadwalModel
            ->select('jadwal_supervisi.id as jadwal_id, jadwal_supervisi.tanggal_supervisi, jadwal_supervisi.mata_pelajaran')
            ->where('jadwal_supervisi.guru_id', $guruId)
            ->where('jadwal_supervisi.status', 'Selesai');

        if ($tahunAjarId && is_numeric($tahunAjarId)) {
            $schedulesQuery->where('jadwal_supervisi.tahun_ajar_id', $tahunAjarId);
        }

        $schedulesQuery->orderBy('jadwal_supervisi.tanggal_supervisi', 'ASC');
        $schedules = $schedulesQuery->findAll();

        // Debug logging
        if (ENVIRONMENT === 'development') {
            log_message('debug', "Teacher ID: $guruId, Schedules found: " . count($schedules));
            if (!empty($schedules)) {
                log_message('debug', "Sample schedule data: " . json_encode(array_slice($schedules, 0, 2)));
            }
        }

        if (!empty($schedules)) {
            $totalScore = 0;
            $count = 0;
            $subjects = [];
            $trendData = [];

            foreach ($schedules as $schedule) {
                // For each schedule, we need to calculate the nilai_akhir from detail results
                // Get all hasil supervisi for this schedule
                $hasilList = $hasilModel
                    ->where('jadwal_supervisi_id', $schedule['jadwal_id'])
                    ->findAll();
                
                if (!empty($hasilList)) {
                    // Calculate nilai_akhir for this schedule
                    $scheduleTotalSkor = 0;
                    $scheduleJumlahAspek = 0;
                    
                    foreach ($hasilList as $hasil) {
                        $details = $detailModel
                            ->where('hasil_supervisi_id', $hasil['id'])
                            ->findAll();
                        
                        foreach ($details as $detail) {
                            $scheduleTotalSkor += $detail['skor'];
                            $scheduleJumlahAspek++;
                        }
                    }
                    
                    // Calculate nilai_akhir (max skor per aspek = 4)
                    $nilaiAkhir = 0;
                    if ($scheduleJumlahAspek > 0) {
                        $nilaiAkhir = ($scheduleTotalSkor / ($scheduleJumlahAspek * 4)) * 100;
                    }
                    
                    // Only count valid nilai_akhir values
                    if ($nilaiAkhir > 0) {
                        $totalScore += $nilaiAkhir;
                        $count++;
                    }
                    
                    // Add to trend data
                    $trendData[] = [
                        'tanggal' => $schedule['tanggal_supervisi'],
                        'nilai' => $nilaiAkhir
                    ];
                }
                
                // Collect subjects
                if (!in_array($schedule['mata_pelajaran'], $subjects)) {
                    $subjects[] = $schedule['mata_pelajaran'];
                }
            }

            // Calculate average score with proper handling
            $averageScore = $count > 0 ? $totalScore / $count : 0;

            $result = [
                'rata_rata' => $averageScore,
                'jumlah_supervisi' => $count,
                'trend' => $trendData,
                'subjects' => $subjects
            ];
            
            // Debug logging
            if (ENVIRONMENT === 'development') {
                log_message('debug', "Teacher ID: $guruId, Result: " . json_encode($result));
            }
            
            return $result;
        } else {
            return [
                'rata_rata' => 0,
                'jumlah_supervisi' => 0,
                'trend' => [],
                'subjects' => []
            ];
        }
    }
}

if (!function_exists('get_teacher_performance_category')) {
    /**
     * Menentukan kategori kinerja berdasarkan nilai rata-rata
     * 
     * @param float $averageScore Nilai rata-rata
     * @return array Informasi kategori dan class CSS
     */
    function get_teacher_performance_category($averageScore) {
        if ($averageScore >= 86) {
            return [
                'kategori' => 'Baik Sekali',
                'kategori_class' => 'success'
            ];
        } elseif ($averageScore >= 70) {
            return [
                'kategori' => 'Baik',
                'kategori_class' => 'info'
            ];
        } elseif ($averageScore >= 55) {
            return [
                'kategori' => 'Cukup',
                'kategori_class' => 'warning'
            ];
        } else {
            return [
                'kategori' => 'Kurang',
                'kategori_class' => 'danger'
            ];
        }
    }
}