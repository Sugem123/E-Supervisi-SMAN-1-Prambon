<?php

namespace App\Controllers\Supervisor;

use App\Controllers\BaseController;
use App\Models\JadwalSupervisiModel;
use App\Models\FotoBuktiModel;
use App\Models\GuruModel;
use CodeIgniter\Files\File;

class FotoBuktiController extends BaseController
{
    protected $jadwalModel;
    protected $fotoModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->fotoModel = new FotoBuktiModel();
        $this->guruModel = new GuruModel();
    }

    public function uploadForm($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Get schedule details with teacher information
        $schedule = $this->jadwalModel
            ->select('jadwal_supervisi.*, guru.nama as nama_guru, guru.mata_pelajaran as guru_mata_pelajaran')
            ->join('guru', 'guru.id = jadwal_supervisi.guru_id')
            ->where('jadwal_supervisi.id', $jadwalId)
            ->where('jadwal_supervisi.supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jadwal tidak ditemukan');
        }

        // Get existing photos
        $existingPhotos = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->findAll();

        $data = [
            'schedule' => $schedule,
            'existingPhotos' => $existingPhotos
        ];

        return view('supervisor/foto_bukti/upload', $data);
    }

    public function upload($jadwalId)
    {
        $supervisorId = session()->get('id');

        // Validate schedule
        $schedule = $this->jadwalModel
            ->where('id', $jadwalId)
            ->where('supervisor_id', $supervisorId)
            ->first();

        if (!$schedule) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Jadwal tidak ditemukan'
            ]);
        }

        // Check if files were uploaded
        $files = $this->request->getFiles();
        
        if (empty($files['photos'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada file yang diupload'
            ]);
        }

        $photos = $files['photos'];
        $uploadedPhotos = [];
        $errors = [];

        // Get existing photos count
        $existingPhotosCount = $this->fotoModel
            ->where('jadwal_supervisi_id', $jadwalId)
            ->countAllResults();

        // Limit to 5 photos total
        $totalPhotos = $existingPhotosCount + count($photos);
        if ($totalPhotos > 5) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Maksimal hanya 5 foto yang dapat diupload untuk jadwal ini',
                'csrf_token' => csrf_hash(),
                'token' => csrf_hash()
            ]);
        }

        // Get keterangan from request
        $keterangan = $this->request->getPost('keterangan');

        // Create directory structure based on guru_id
        $guruId = $schedule['guru_id'];
        $uploadPath = ROOTPATH . 'public/uploads/foto_bukti/guru_' . $guruId;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($photos as $index => $file) {
            // Skip if no file uploaded for this index
            if ($file->isValid() && !$file->hasMoved()) {
                // Validate file type
                $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    $errors[] = "File " . ($index + 1) . " bukan gambar yang valid (jpg, jpeg, png, webp)";
                    continue;
                }

                // Validate file size (max 3MB)
                if ($file->getSize() > 3072 * 1024) {
                    $errors[] = "File " . ($index + 1) . " terlalu besar (maksimal 3MB)";
                    continue;
                }

                // Generate unique filename
                $newName = $file->getRandomName();
                $relativePath = 'uploads/foto_bukti/guru_' . $guruId . '/' . $newName;
                $targetPath = $uploadPath . '/' . $newName;

                // Move file to target directory
                if ($file->move($uploadPath, $newName)) {
                    // Save to database
                    $this->fotoModel->save([
                        'jadwal_supervisi_id' => $jadwalId,
                        'file_path' => $relativePath,
                        'keterangan' => $keterangan,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    $uploadedPhotos[] = $relativePath;
                } else {
                    $errors[] = "Gagal menyimpan file " . ($index + 1);
                }
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => implode(', ', $errors),
                'csrf_token' => csrf_hash(),
                'token' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Foto berhasil diupload',
            'redirect' => base_url('supervisor/foto-bukti/upload/' . $jadwalId),
            'csrf_token' => csrf_hash(),
            'token' => csrf_hash()
        ]);
    }

    public function delete($fotoId)
    {
        $foto = $this->fotoModel->find($fotoId);

        if (!$foto) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Foto tidak ditemukan',
                'csrf_token' => csrf_hash(),
                'token' => csrf_hash()
            ]);
        }

        // Delete file from filesystem
        $filePath = ROOTPATH . 'public/' . $foto['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $this->fotoModel->delete($fotoId);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Foto berhasil dihapus',
            'csrf_token' => csrf_hash(),
            'token' => csrf_hash()
        ]);
    }
}