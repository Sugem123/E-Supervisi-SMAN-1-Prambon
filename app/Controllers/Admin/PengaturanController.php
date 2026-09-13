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
        $data['identitas'] = [
            'nama_madrasah' => session()->get('nama_madrasah') ?? $this->settingModel->getSetting('nama_madrasah', 'MIN 2 TANGGAMUS'),
            'nsm' => session()->get('nsm') ?? $this->settingModel->getSetting('nsm', '111118060002'),
            'npsn' => session()->get('npsn') ?? $this->settingModel->getSetting('npsn', '60705691'),
            'alamat' => session()->get('alamat') ?? $this->settingModel->getSetting('alamat', 'Jl. Lapangan Ampera Purwodadi No. 109'),
            'kecamatan' => session()->get('kecamatan') ?? $this->settingModel->getSetting('kecamatan', 'GISTING'),
            'kabupaten' => session()->get('kabupaten') ?? $this->settingModel->getSetting('kabupaten', 'KABUPATEN TANGGAMUS'),
            'provinsi' => session()->get('provinsi') ?? $this->settingModel->getSetting('provinsi', 'LAMPUNG'),

            'nama_kepala' => session()->get('nama_kepala') ?? $this->settingModel->getSetting('nama_kepala', 'Sipuloh, M.Pd'),
            'nip_kepala' => session()->get('nip_kepala') ?? $this->settingModel->getSetting('nip_kepala', '197005272007011022'),

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
        return redirect()->to('/admin/pengaturan?tab=identitas');
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
        $rules = [
            'nama_madrasah' => 'required',
            'nsm' => 'required',
            'npsn' => 'required',
            'alamat' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_madrasah' => $this->request->getPost('nama_madrasah'),
            'nsm' => $this->request->getPost('nsm'),
            'npsn' => $this->request->getPost('npsn'),
            'alamat' => $this->request->getPost('alamat'),
            'kecamatan' => $this->request->getPost('kecamatan'),
            'kabupaten' => $this->request->getPost('kabupaten'),
            'provinsi' => $this->request->getPost('provinsi'),
        ];

        foreach ($data as $key => $value) {
            $this->settingModel->setSetting($key, $value);
            session()->set($key, $value);
        }

        return redirect()->to('/admin/pengaturan?tab=identitas')->with('success', 'Identitas Madrasah berhasil diperbarui.');
    }
}
