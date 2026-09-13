<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\FotoBuktiModel;
use App\Models\GuruModel;
use App\Models\AuditLogModel;

class FotoBuktiController extends BaseController
{
    protected $jadwalModel;
    protected $fotoModel;
    protected $guruModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->fotoModel = new FotoBuktiModel();
        $this->guruModel = new GuruModel();
        $this->auditLogModel = new AuditLogModel();
    }

    public function uploadForm($jadwalId)
    {
        // Admin dapat melihat dan mengunggah foto bukti untuk jadwal apapun
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.nip as nip_guru, guru.mata_pelajaran as guru_mata_pelajaran, users.username as nama_supervisor, tahun_ajar.tahun_ajar, tahun_ajar.semester')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->join('tahun_ajar', 'tahun_ajar.id = jadwal_supervisi.tahun_ajar_id', 'left')
            ->join('users', 'users.id = jadwal_supervisi.supervisor_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal supervisi ID ' . $jadwalId . ' tidak ditemukan');
        }

        // Ambil foto bukti yang sudah diupload
        $existingPhotos = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title'          => 'Upload Foto Bukti Supervisi',
            'schedule'       => $schedule,
            'existingPhotos' => $existingPhotos
        ];

        return view('admin/foto_bukti/upload', $data);
    }

    public function upload($jadwalId)
    {
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        if (!$schedule) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Jadwal supervisi tidak ditemukan',
                'token'   => csrf_hash()
            ]);
        }

        $files = $this->request->getFiles();

        if (empty($files['photos'])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada file foto yang dipilih untuk diupload',
                'token'   => csrf_hash()
            ]);
        }

        $photos = $files['photos'];
        $uploadedPhotos = [];
        $errors = [];

        // Hitung jumlah foto yang sudah ada
        $existingPhotosCount = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->countAllResults();

        // Hitung file valid yang dikirim
        $validFileCount = 0;
        foreach ($photos as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $validFileCount++;
            }
        }

        if ($validFileCount === 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tidak ada file foto valid yang dipilih',
                'token'   => csrf_hash()
            ]);
        }

        // Batas maksimal 5 foto total untuk admin
        $maxPhotos = 5;
        if ($existingPhotosCount + $validFileCount > $maxPhotos) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => "Maksimal hanya {$maxPhotos} foto yang dapat diupload untuk satu jadwal (saat ini sudah ada {$existingPhotosCount} foto)",
                'token'   => csrf_hash()
            ]);
        }

        $keterangan = $this->request->getPost('keterangan');

        // Path penyimpanan berdasarkan guru_id
        $guruId = $schedule['guru_id'];
        $uploadPath = ROOTPATH . 'public/uploads/foto_bukti/guru_' . $guruId;

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($photos as $index => $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                // Validasi tipe file gambar
                $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    $errors[] = "File #" . ($index + 1) . " bukan gambar valid (jpg, jpeg, png, webp)";
                    continue;
                }

                // Validasi ukuran file (maksimal 3MB)
                if ($file->getSize() > 3072 * 1024) {
                    $errors[] = "File #" . ($index + 1) . " terlalu besar (maksimal 3MB)";
                    continue;
                }

                $newName = $file->getRandomName();
                $relativePath = 'uploads/foto_bukti/guru_' . $guruId . '/' . $newName;

                if ($file->move($uploadPath, $newName)) {
                    $this->fotoModel->save([
                        'jadwal_supervisi_id' => $jadwalId,
                        'file_path'           => $relativePath,
                        'keterangan'          => $keterangan,
                        'created_at'          => date('Y-m-d H:i:s')
                    ]);
                    $uploadedPhotos[] = $relativePath;
                } else {
                    $errors[] = "Gagal menyimpan file #" . ($index + 1);
                }
            }
        }

        if (!empty($errors) && empty($uploadedPhotos)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => implode(', ', $errors),
                'token'   => csrf_hash()
            ]);
        }

        // Catat Audit Log
        $userId = session()->get('id');
        $guruNama = $schedule['nama_guru'] ?? ('ID ' . $schedule['guru_id']);
        $countUploaded = count($uploadedPhotos);
        $this->auditLogModel->logActivity(
            $userId,
            'upload_foto_bukti',
            "Admin mengupload {$countUploaded} foto bukti supervisi untuk guru {$guruNama} (Jadwal #{$jadwalId})"
        );

        $msg = "{$countUploaded} foto berhasil diupload";
        if (!empty($errors)) {
            $msg .= " (Catatan: " . implode(', ', $errors) . ")";
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => $msg,
            'redirect' => base_url('admin/foto-bukti/upload/' . $jadwalId),
            'token'    => csrf_hash()
        ]);
    }

    public function delete($fotoId)
    {
        $foto = $this->fotoModel->find($fotoId);

        if (!$foto) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Foto bukti tidak ditemukan',
                'token'   => csrf_hash()
            ]);
        }

        $jadwalId = $foto['jadwal_supervisi_id'];
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->first();

        // Hapus berkas fisik dari filesystem jika ada
        $filePath = ROOTPATH . 'public/' . $foto['file_path'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        // Hapus dari database
        $this->fotoModel->delete($fotoId);

        // Catat Audit Log
        $userId = session()->get('id');
        $guruNama = $schedule['nama_guru'] ?? ('Jadwal #' . $jadwalId);
        $this->auditLogModel->logActivity(
            $userId,
            'delete_foto_bukti',
            "Admin menghapus foto bukti supervisi (#{$fotoId}) untuk guru {$guruNama}"
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Foto bukti berhasil dihapus',
            'token'   => csrf_hash()
        ]);
    }
}
