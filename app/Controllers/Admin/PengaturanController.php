<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemSettingModel;
use App\Models\UserModel;
use App\Models\TahunAjarModel;

class PengaturanController extends BaseController
{
    protected $settingModel;
    protected $userModel;
    protected $tahunAjarModel;

    public function __construct()
    {
        $this->settingModel = new SystemSettingModel();
        $this->userModel = new UserModel();
        $this->tahunAjarModel = new TahunAjarModel();
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'identitas';
        if (!in_array($activeTab, ['identitas', 'kop', 'tahun-ajar'])) {
            $activeTab = 'identitas';
        }

        $data['title'] = 'Pengaturan Sistem';
        $data['activeTab'] = $activeTab;
        $namaSekolah = session()->get('nama_sekolah') ?? $this->settingModel->getSetting('nama_sekolah', null);
        if (empty($namaSekolah)) {
            $namaSekolah = session()->get('nama_madrasah') ?? $this->settingModel->getSetting('nama_madrasah', 'SMA NEGERI 1 CONTOH');
        }

        $data['identitas'] = [
            'nama_sekolah' => $namaSekolah,
            'nama_madrasah' => $namaSekolah,
            'npsn' => session()->get('npsn') ?? $this->settingModel->getSetting('npsn', ''),
            'alamat' => session()->get('alamat') ?? $this->settingModel->getSetting('alamat', ''),
            'kecamatan' => session()->get('kecamatan') ?? $this->settingModel->getSetting('kecamatan', ''),
            'kabupaten' => session()->get('kabupaten') ?? $this->settingModel->getSetting('kabupaten', ''),
            'provinsi' => session()->get('provinsi') ?? $this->settingModel->getSetting('provinsi', ''),

            'nama_kepala' => session()->get('nama_kepala') ?? $this->settingModel->getSetting('nama_kepala', ''),
            'nip_kepala' => session()->get('nip_kepala') ?? $this->settingModel->getSetting('nip_kepala', ''),

            'telepon' => session()->get('telepon') ?? $this->settingModel->getSetting('telepon', ''),
            'email' => session()->get('email') ?? $this->settingModel->getSetting('email', ''),
            'logo' => session()->get('logo') ?? $this->settingModel->getSetting('logo', ''),
            'kop_surat' => session()->get('kop_surat') ?? $this->settingModel->getSetting('kop_surat', ''),
            'sidebar_logo' => session()->get('sidebar_logo') ?? $this->settingModel->getSetting('sidebar_logo', '')
        ];
        $data['kop'] = get_kop_data();
        $data['tahun_ajar'] = $this->tahunAjarModel->orderBy('tahun_ajar', 'DESC')->findAll();

        return view('admin/pengaturan/index', $data);
    }

    public function identitasMadrasah()
    {
        // Legacy route alias kept for old bookmarks/forms; canonical tab is Identitas Sekolah.
        return redirect()->to('/admin/pengaturan?tab=identitas');
    }

    public function updateIdentitasMadrasah()
    {
        // Legacy form endpoint alias; delegate to canonical SMA handler.
        return $this->updateIdentitas();
    }

    public function updatePimpinan()
    {
        $rules = [
            'nama_kepala' => 'required',
            'nip_kepala' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_kepala' => $this->request->getPost('nama_kepala'),
            'nip_kepala' => $this->request->getPost('nip_kepala'),
        ];

        foreach ($data as $key => $value) {
            $this->settingModel->setSetting($key, $value);
            session()->set($key, $value);
        }

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Data Pimpinan berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $rules = [
            'password_baru' => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('id');
        if (!$userId) {
            return redirect()->to('/auth/login')->with('error', 'Silakan login kembali.');
        }

        $user = $this->userModel->find($userId);

        if ($user) {
            $data = ['password' => password_hash($this->request->getPost('password_baru'), PASSWORD_DEFAULT)];
            $this->userModel->update($userId, $data);
        }

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Password berhasil diperbarui.');
    }

    public function uploadAsset($type)
    {
        if (!in_array($type, ['logo', 'kop_surat', 'sidebar_logo', 'kop_logo_kiri', 'kop_logo_kanan'])) {
            return redirect()->back()->with('error', 'Tipe aset tidak valid.');
        }

        $validation = \Config\Services::validation();
        $rules = [
            $type => "uploaded[$type]|is_image[$type]|max_size[$type,2048]"
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Gagal upload ' . $type . '. ' . $validation->listErrors());
        }

        $file = $this->request->getFile($type);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old file
            $oldFile = $this->settingModel->getSetting($type, '');
            if ($oldFile && file_exists(ROOTPATH . 'public/uploads/' . $oldFile)) {
                unlink(ROOTPATH . 'public/uploads/' . $oldFile);
            }

            $fileName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $fileName);
            $this->settingModel->setSetting($type, $fileName);
            session()->set($type, $fileName);
        }

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', ucfirst(str_replace('_', ' ', $type)) . ' berhasil diupload.');
    }

    public function deleteLogo($type)
    {
        if (!in_array($type, ['logo', 'kop_surat', 'sidebar_logo', 'kop_logo_kiri', 'kop_logo_kanan'])) {
            return redirect()->back()->with('error', 'Tipe logo tidak valid.');
        }

        $fileName = session()->get($type) ?? $this->settingModel->getSetting($type);

        if ($fileName && file_exists(ROOTPATH . 'public/uploads/' . $fileName)) {
            unlink(ROOTPATH . 'public/uploads/' . $fileName);
        }

        session()->remove($type);
        $this->settingModel->deleteSetting($type);

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Logo berhasil dihapus.');
    }

    public function updateKop()
    {
        $rules = [
            'kop_baris_1' => 'required',
            'kop_baris_2' => 'permit_empty',
            'kop_baris_3' => 'required',
            'kop_baris_4' => 'permit_empty',
            'kop_baris_5' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'kop_baris_1' => $this->request->getPost('kop_baris_1'),
            'kop_baris_2' => $this->request->getPost('kop_baris_2'),
            'kop_baris_3' => $this->request->getPost('kop_baris_3'),
            'kop_baris_4' => $this->request->getPost('kop_baris_4'),
            'kop_baris_5' => $this->request->getPost('kop_baris_5'),
            'kop_tampilkan_logo' => $this->request->getPost('kop_tampilkan_logo') ? '1' : '0',
            'kop_tampilkan_garis' => $this->request->getPost('kop_tampilkan_garis') ? '1' : '0',
        ];

        foreach ($data as $key => $value) {
            $this->settingModel->setSetting($key, $value);
            session()->set($key, $value);
        }

        return redirect()->to('/admin/pengaturan?tab=kop')->with('success', 'Pengaturan Kop Instansi berhasil diperbarui.');
    }

    public function syncProfile()
    {
        // Placeholder for sync logic
        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Sinkronisasi profil berhasil (Simulasi).');
    }

    public function updateIdentitas()
    {
        // Accept both canonical 'nama_sekolah' and legacy 'nama_madrasah' posts.
        if ($this->request->getPost('nama_sekolah') === null && $this->request->getPost('nama_madrasah') !== null) {
            $this->request->setGlobal('post', array_merge($this->request->getPost(), [
                'nama_sekolah' => $this->request->getPost('nama_madrasah'),
            ]));
        }

        $rules = [
            'nama_sekolah' => 'required',
            'npsn' => 'permit_empty|max_length[20]',
            'alamat' => 'permit_empty',
            'kecamatan' => 'permit_empty',
            'kabupaten' => 'permit_empty',
            'provinsi' => 'permit_empty',
            'telepon' => 'permit_empty|max_length[30]',
            'email' => 'permit_empty|valid_email|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $namaSekolah = trim((string) $this->request->getPost('nama_sekolah'));
        if ($namaSekolah === '') {
            $namaSekolah = trim((string) $this->request->getPost('nama_madrasah'));
        }

        $data = [
            'nama_sekolah' => $namaSekolah,
            'nama_madrasah' => $namaSekolah,
            'npsn' => $this->request->getPost('npsn'),
            'alamat' => $this->request->getPost('alamat'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'kabupaten' => $this->request->getPost('kabupaten'),
            'provinsi' => $this->request->getPost('provinsi'),
            'telepon' => $this->request->getPost('telepon'),
            'email' => $this->request->getPost('email'),
        ];

        foreach ($data as $key => $value) {
            $this->settingModel->setSetting($key, $value);
            session()->set($key, $value);
        }

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Identitas Sekolah berhasil diperbarui.');
    }
}
