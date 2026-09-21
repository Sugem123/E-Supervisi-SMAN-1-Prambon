<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\RefMapelModel;
use App\Models\SupervisorModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PenggunaController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $supervisorModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->supervisorModel = new SupervisorModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel
            ->select('users.*, COALESCE(guru.nama, users.username) as nama_lengkap, guru.nama as nama_guru, COALESCE(guru.nip, users.nip) as nip_gabungan, guru.mata_pelajaran, guru.jenis_ptk')
            ->join('guru', 'guru.user_id = users.id', 'left')
            ->orderBy('users.id', 'ASC')
            ->findAll();
        return view('admin/pengguna/index', $data);
    }

    public function semua()
    {
        $data['users'] = $this->userModel
            ->select('users.*, COALESCE(guru.nama, users.username) as nama_lengkap, guru.nama as nama_guru, COALESCE(guru.nip, users.nip) as nip_gabungan, guru.mata_pelajaran, guru.jenis_ptk')
            ->join('guru', 'guru.user_id = users.id', 'left')
            ->orderBy('users.id', 'ASC')
            ->findAll();
        return view('admin/pengguna/index', $data);
    }

    public function syncGuruUsernames()
    {
        // Get all users who don't have a username yet (NULL or empty) and have a corresponding guru record
        $usersWithoutUsername = $this->userModel
            ->select('users.*, guru.nama as guru_nama')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->groupStart()
            ->where('users.username IS NULL')
            ->orWhere('users.username', '')
            ->groupEnd()
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        // Get all existing usernames to avoid N+1 queries
        $existingUsernames = $this->userModel->findColumn('username') ?? [];
        $existingUsernamesMap = array_flip($existingUsernames);

        $updateData = [];
        $syncCount = 0;

        foreach ($usersWithoutUsername as $user) {
            // Check if this user has a corresponding guru record
            if (!empty($user['guru_nama'])) {
                // Create a base username from the guru's name
                // Basic sanitization: lowercase and replace spaces with underscores
                $baseUsername = strtolower(str_replace(' ', '_', trim($user['guru_nama'])));
                $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);

                $username = $baseUsername;
                $counter = 1;

                // Check if the username already exists in our map
                while (isset($existingUsernamesMap[$username])) {
                    $username = $baseUsername . '_' . $counter;
                    $counter++;

                    // Safety check to prevent infinite loop
                    if ($counter > 100) {
                        break;
                    }
                }

                // Add to map so we don't reuse it in this batch
                $existingUsernamesMap[$username] = true;

                // Add to batch update data
                $updateData[] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $syncCount++;
            }
        }

        if (!empty($updateData)) {
            $this->userModel->updateBatch($updateData, 'id');
        }

        return redirect()->back()->with('success', "$syncCount pengguna berhasil disinkronisasi dengan data guru.");
    }

    public function syncGuruNip()
    {
        // Get all users that have a corresponding guru record
        $users = $this->userModel
            ->select('users.id as user_id, users.nip as user_nip, guru.nip as guru_nip')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $updateData = [];
        $syncCount = 0;

        foreach ($users as $user) {
            // If user's NIP is empty or different from guru's NIP, update it
            if ((empty($user['user_nip']) && !empty($user['guru_nip'])) ||
                (!empty($user['user_nip']) && !empty($user['guru_nip']) && $user['user_nip'] != $user['guru_nip'])
            ) {

                $updateData[] = [
                    'id' => $user['user_id'],
                    'nip' => $user['guru_nip']
                ];
                $syncCount++;
            }
        }

        if (!empty($updateData)) {
            $this->userModel->updateBatch($updateData, 'id');
        }

        return redirect()->back()->with('success', "$syncCount pengguna berhasil disinkronisasi NIP-nya dengan data guru.");
    }

    public function syncGuruData()
    {
        // 1. Sync Usernames
        $usersWithoutUsername = $this->userModel
            ->select('users.*, guru.nama as guru_nama')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->groupStart()
            ->where('users.username IS NULL')
            ->orWhere('users.username', '')
            ->groupEnd()
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $existingUsernames = $this->userModel->findColumn('username') ?? [];
        $existingUsernamesMap = array_flip($existingUsernames);

        $usernameUpdateData = [];
        $usernameSyncCount = 0;

        foreach ($usersWithoutUsername as $user) {
            if (!empty($user['guru_nama'])) {
                $baseUsername = strtolower(str_replace(' ', '_', trim($user['guru_nama'])));
                $baseUsername = preg_replace('/[^a-z0-9_]/', '', $baseUsername);

                $username = $baseUsername;
                $counter = 1;

                while (isset($existingUsernamesMap[$username])) {
                    $username = $baseUsername . '_' . $counter;
                    $counter++;
                    if ($counter > 100) break;
                }

                $existingUsernamesMap[$username] = true;

                $usernameUpdateData[] = [
                    'id' => $user['id'],
                    'username' => $username
                ];
                $usernameSyncCount++;
            }
        }

        if (!empty($usernameUpdateData)) {
            $this->userModel->updateBatch($usernameUpdateData, 'id');
        }

        // 2. Sync NIPs
        $users = $this->userModel
            ->select('users.id as user_id, users.nip as user_nip, guru.nip as guru_nip')
            ->join('guru', 'users.id = guru.user_id', 'left')
            ->where('guru.user_id IS NOT NULL')
            ->findAll();

        $nipUpdateData = [];
        $nipSyncCount = 0;

        foreach ($users as $user) {
            if ((empty($user['user_nip']) && !empty($user['guru_nip'])) ||
                (!empty($user['user_nip']) && !empty($user['guru_nip']) && $user['user_nip'] != $user['guru_nip'])
            ) {

                $nipUpdateData[] = [
                    'id' => $user['user_id'],
                    'nip' => $user['guru_nip']
                ];
                $nipSyncCount++;
            }
        }

        if (!empty($nipUpdateData)) {
            $this->userModel->updateBatch($nipUpdateData, 'id');
        }

        return redirect()->back()->with('success', "$usernameSyncCount username dan $nipSyncCount NIP pengguna berhasil disinkronisasi dengan data guru.");
    }

    public function guru()
    {
        $data['gurus'] = $this->guruModel
            ->select('guru.*, guru.user_id as account_id, users.username, users.email, guru.is_supervisor, ref_mapel.nama_mapel as nama_mapel_ref')
            ->join('users', 'users.id = guru.user_id', 'left')
            ->join('ref_mapel', 'ref_mapel.id = guru.mapel_id', 'left')
            ->findAll();
        return view('admin/pengguna/guru', $data);
    }

    public function supervisor()
    {
        $data['supervisors'] = $this->supervisorModel
            ->select('supervisor.*, users.username, users.email, guru.nama as nama_guru')
            ->join('users', 'users.id = supervisor.user_id', 'left')
            ->join('guru', 'guru.user_id = supervisor.user_id', 'left')
            ->findAll();
        return view('admin/pengguna/supervisor', $data);
    }

    public function create()
    {
        $data['mapels'] = $this->getActiveMapels();
        return view('admin/pengguna/create', $data);
    }

    private function getActiveMapels(): array
    {
        try {
            if (!\Config\Database::connect()->tableExists('ref_mapel')) {
                return [];
            }
            return (new RefMapelModel())->where('status', 'Aktif')->orderBy('nama_mapel', 'ASC')->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function resolveMapelId($mapelId, $mapelName = null)
    {
        if ($mapelId !== null && $mapelId !== '') {
            return (int) $mapelId;
        }
        if ($mapelName !== null && $mapelName !== '') {
            try {
                if (!\Config\Database::connect()->tableExists('ref_mapel')) {
                    return null;
                }
                $row = (new RefMapelModel())->where('nama_mapel', trim((string) $mapelName))->first();
                return $row['id'] ?? null;
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,kepala,supervisor,guru]',
            'jenis_ptk' => 'permit_empty|in_list[Guru,Tendik]',
            'mapel_id' => 'permit_empty|integer',
            'status_kepegawaian' => 'permit_empty|in_list[PNS,PPPK,GTT,PTT,Honorer,Kontrak]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if is_supervisor checkbox is checked
        $isSupervisor = ($this->request->getPost('role') == 'guru' && $this->request->getPost('is_supervisor')) ? 1 : 0;

        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $isSupervisor ? 'supervisor' : $this->request->getPost('role'), // Set role to supervisor if is_supervisor is checked
            'status' => 'Aktif',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // If role is guru or supervisor, create guru record
                if ($this->request->getPost('role') == 'guru' || $isSupervisor) {
                    $guruData = [
                        'user_id' => $userId,
                        'nama' => $this->request->getPost('nama_lengkap'),
                        'nip' => $this->request->getPost('nip'),
                        'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
                        'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
                        'mapel_id' => $this->resolveMapelId($this->request->getPost('mapel_id'), $this->request->getPost('mata_pelajaran')),
                        'jenis_ptk' => $this->request->getPost('jenis_ptk') ?: 'Guru',
                        'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
                        'is_supervisor' => $isSupervisor, // Set is_supervisor field
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $this->guruModel->insert($guruData);

                    // If is_supervisor is checked, also create supervisor record
                    if ($isSupervisor) {
                        $supervisorData = [
                            'user_id' => $userId,
                            'tanggal_penugasan' => date('Y-m-d'),
                            'status' => 'Aktif'
                        ];
                        $this->supervisorModel->insert($supervisorData);
                    }
                }

                return redirect()->to('/admin/pengguna')->with('success', 'User berhasil ditambahkan');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user');
            }
        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('error', 'Username atau email sudah digunakan. Silakan gunakan username atau email lain.');
            } else {
                // For other exceptions, show a generic error message
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            }
        }
    }

    public function edit($id)
    {
        $data['user'] = $this->userModel->find($id);
        return view('admin/pengguna/edit', $data);
    }

    public function editGuru($id = null)
    {
        if (!$id) {
            return redirect()->to('/admin/pengguna/guru')->with('error', 'ID Pengguna tidak valid');
        }

        $data['user'] = $this->userModel->find($id);

        if (!$data['user']) {
            return redirect()->to('/admin/pengguna/guru')->with('error', 'User tidak ditemukan');
        }

        // Check if user actually has role 'guru'
        if ($data['user']['role'] != 'guru') {
            // Redirect to the general edit page if user is not a guru
            return redirect()->to('/admin/pengguna/' . $id . '/edit');
        }

        $data['guru'] = $this->guruModel->where('user_id', $id)->first();
        $data['mapels'] = $this->getActiveMapels();
        return view('admin/pengguna/edit_guru', $data);
    }

    public function editSupervisor($id)
    {
        $data['user'] = $this->userModel->find($id);
        $data['supervisor'] = $this->supervisorModel->where('user_id', $id)->first();
        $data['gurus'] = $this->guruModel->findAll();
        return view('admin/pengguna/edit_supervisor', $data);
    }

    public function update($id)
    {
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        try {
            $result = $this->userModel->update($id, $userData);

            if ($result) {
                return redirect()->to('/admin/pengguna')->with('success', 'User berhasil diupdate');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengupdate user');
            }
        } catch (\Exception $e) {
            // Check if it's a duplicate entry error
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return redirect()->back()->withInput()->with('error', 'Username atau email sudah digunakan. Silakan gunakan username atau email lain.');
            } else {
                // For other exceptions, show a generic error message
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
            }
        }
    }

    public function updateGuru($id)
    {
        // Get user data
        $user = $this->userModel->find($id);

        // Prevent updating non-guru users through this method
        if ($user['role'] != 'guru' && $user['role'] != 'supervisor') {
            return redirect()->back()->with('error', 'User ini bukan guru atau supervisor, tidak dapat diupdate melalui form guru');
        }

        // Validation rules
        $validation = \Config\Services::validation();

        $rules = [
            'nama' => 'required',
            'jenis_ptk' => 'permit_empty|in_list[Guru,Tendik]',
            'mapel_id' => 'permit_empty|integer',
            'status_kepegawaian' => 'permit_empty|in_list[PNS,PPPK,GTT,PTT,Honorer,Kontrak]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if is_supervisor checkbox is checked
        $isSupervisor = $this->request->getPost('is_supervisor') ? 1 : 0;

        // Update user data (email is kept as is)
        $userData = [
            'status' => $this->request->getPost('status'),
            'role' => $isSupervisor ? 'supervisor' : 'guru' // Set role based on is_supervisor
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userResult = $this->userModel->update($id, $userData);

        // Update guru data
        $guruData = [
            'nama' => $this->request->getPost('nama'),
            'nip' => $this->request->getPost('nip'),
            'pangkat_golongan' => $this->request->getPost('pangkat_golongan'),
            'mata_pelajaran' => $this->request->getPost('mata_pelajaran'),
            'mapel_id' => $this->resolveMapelId($this->request->getPost('mapel_id'), $this->request->getPost('mata_pelajaran')),
            'jenis_ptk' => $this->request->getPost('jenis_ptk') ?: 'Guru',
            'status_kepegawaian' => $this->request->getPost('status_kepegawaian'),
            'is_supervisor' => $isSupervisor // Set is_supervisor field
        ];

        // Check if guru record exists for this user
        $guruRecord = $this->guruModel->where('user_id', $id)->first();

        if ($guruRecord) {
            // Update existing guru record
            $guruResult = $this->guruModel->where('user_id', $id)->set($guruData)->update();
        } else {
            // Create new guru record if it doesn't exist
            $guruData['user_id'] = $id;
            $guruResult = $this->guruModel->insert($guruData);
        }

        // Handle role change to supervisor
        if ($isSupervisor) {
            // Check if supervisor record exists for this user
            $supervisorRecord = $this->supervisorModel->where('user_id', $id)->first();

            if (!$supervisorRecord) {
                // Create supervisor record if it doesn't exist
                $supervisorData = [
                    'user_id' => $id,
                    'tanggal_penugasan' => date('Y-m-d'),
                    'status' => 'Aktif'
                ];
                $this->supervisorModel->insert($supervisorData);
            }
        }

        if ($userResult && $guruResult) {
            // Redirect based on the new role
            if ($isSupervisor) {
                return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Guru berhasil diupdate dan dijadikan supervisor');
            } else {
                return redirect()->to('/admin/pengguna/guru')->with('success', 'Guru berhasil diupdate');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate guru');
        }
    }

    public function updateSupervisor($id)
    {
        // Get user data
        $user = $this->userModel->find($id);

        // Prevent updating non-supervisor users through this method
        if ($user['role'] != 'supervisor') {
            return redirect()->back()->with('error', 'User ini bukan supervisor, tidak dapat diupdate melalui form supervisor');
        }

        // Update user data
        $userData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $userData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        // Remove null or empty values to prevent "There is no data to update" error
        $userData = array_filter($userData, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $userResult = true; // Default to true, will be updated if we actually perform an update

        // Only update if there's data to update
        if (!empty($userData)) {
            $userResult = $this->userModel->update($id, $userData);
        }

        // Update supervisor data
        $supervisorData = [
            'tanggal_penugasan' => $this->request->getPost('tanggal_penugasan'),
            'status' => $this->request->getPost('supervisor_status')
        ];

        // Remove null or empty values to prevent "There is no data to update" error
        $supervisorData = array_filter($supervisorData, function ($value) {
            return !is_null($value) && $value !== '';
        });

        $supervisorResult = true; // Default to true, will be updated if we actually perform an update

        // Check if supervisor record exists for this user
        $supervisorRecord = $this->supervisorModel->where('user_id', $id)->first();

        if ($supervisorRecord) {
            // Update existing supervisor record only if there's data to update
            if (!empty($supervisorData)) {
                $supervisorResult = $this->supervisorModel->where('user_id', $id)->set($supervisorData)->update();
            }
        } else {
            // Create new supervisor record if it doesn't exist
            $supervisorData['user_id'] = $id;
            $supervisorResult = $this->supervisorModel->insert($supervisorData);
        }

        // Handle role change from supervisor to guru
        if ($userData['role'] != 'supervisor') {
            // If role is changed from supervisor to something else, we might want to remove the supervisor record
            // But we'll keep it for now to maintain history
        }

        if ($userResult && $supervisorResult) {
            // Redirect based on the new role
            if (isset($userData['role']) && $userData['role'] == 'guru') {
                return redirect()->to('/admin/pengguna/guru')->with('success', 'Supervisor berhasil diupdate dan dikembalikan menjadi guru');
            } else {
                return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Supervisor berhasil diupdate');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate supervisor');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->to('/admin/pengguna')->with('error', 'ID Pengguna tidak valid');
        }

        $result = $this->userModel->delete($id);

        if ($result) {
            return redirect()->to('/admin/pengguna')->with('success', 'User berhasil dihapus');
        } else {
            return redirect()->to('/admin/pengguna')->with('error', 'Gagal menghapus user');
        }
    }

    public function show($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/pengguna')->with('error', 'User tidak ditemukan');
        }

        $data['user'] = $user;

        // Load related data based on role
        if ($user['role'] === 'guru') {
            $data['guru'] = $this->guruModel->where('user_id', $id)->first();
        } elseif ($user['role'] === 'supervisor') {
            $data['supervisor'] = $this->supervisorModel->where('user_id', $id)->first();
        }

        return view('admin/pengguna/show', $data);
    }

    public function makeSupervisor($id)
    {
        // Get guru data
        $guru = $this->guruModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Update guru to set is_supervisor = 1
        $this->guruModel->update($id, ['is_supervisor' => 1]);

        // Update user role to supervisor
        $this->userModel->update($guru['user_id'], ['role' => 'supervisor']);

        // Check if supervisor record exists
        $supervisorRecord = $this->supervisorModel->where('user_id', $guru['user_id'])->first();

        if (!$supervisorRecord) {
            // Create supervisor record
            $supervisorData = [
                'user_id' => $guru['user_id'],
                'tanggal_penugasan' => date('Y-m-d'),
                'status' => 'Aktif'
            ];
            $this->supervisorModel->insert($supervisorData);
        }

        return redirect()->to('/admin/pengguna/supervisor')->with('success', 'Guru berhasil diangkat menjadi supervisor.');
    }

    public function importGuru()
    {
        return view('admin/pengguna/import_guru');
    }

    public function downloadTemplate()
    {
        // Create new Spreadsheet object
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'USERNAME');
        $sheet->setCellValue('B1', 'PASSWORD');
        $sheet->setCellValue('C1', 'EMAIL');
        $sheet->setCellValue('D1', 'ROLE');
        $sheet->setCellValue('E1', 'NAMA_LENGKAP');
        $sheet->setCellValue('F1', 'NIP');
        $sheet->setCellValue('G1', 'PANGKAT_GOLONGAN');
        $sheet->setCellValue('H1', 'MATA_PELAJARAN');
        $sheet->setCellValue('I1', 'STATUS_KEPEGAWAIAN');
        $sheet->setCellValue('J1', 'JENIS_PTK');

        // Add data validation for ROLE column
        $roleValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $roleValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $roleValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $roleValidation->setAllowBlank(false);
        $roleValidation->setShowInputMessage(true);
        $roleValidation->setShowErrorMessage(true);
        $roleValidation->setShowDropDown(true);
        $roleValidation->setErrorTitle('Input error');
        $roleValidation->setError('Value is not in list.');
        $roleValidation->setPromptTitle('Pick from list');
        $roleValidation->setPrompt('Please pick a value from the drop-down list.');
        $roleValidation->setFormula1('"guru,supervisor,kepala,admin"');

        // Add data validation for STATUS_KEPEGAWAIAN column
        $statusValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $statusValidation->setAllowBlank(false);
        $statusValidation->setShowInputMessage(true);
        $statusValidation->setShowErrorMessage(true);
        $statusValidation->setShowDropDown(true);
        $statusValidation->setErrorTitle('Input error');
        $statusValidation->setError('Value is not in list.');
        $statusValidation->setPromptTitle('Pick from list');
        $statusValidation->setPrompt('Please pick a value from the drop-down list.');
        $statusValidation->setFormula1('"PNS,PPPK,GTT,PTT,Honorer,Kontrak"');

        $jenisValidation = new \PhpOffice\PhpSpreadsheet\Cell\DataValidation();
        $jenisValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $jenisValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $jenisValidation->setAllowBlank(true);
        $jenisValidation->setShowInputMessage(true);
        $jenisValidation->setShowErrorMessage(true);
        $jenisValidation->setShowDropDown(true);
        $jenisValidation->setErrorTitle('Input error');
        $jenisValidation->setError('Value is not in list.');
        $jenisValidation->setPromptTitle('Pick from list');
        $jenisValidation->setPrompt('Please pick a value from the drop-down list.');
        $jenisValidation->setFormula1('"Guru,Tendik"');

        // Apply data validation
        $sheet->setDataValidation('D2:D1000', $roleValidation);
        $sheet->setDataValidation('I2:I1000', $statusValidation);
        $sheet->setDataValidation('J2:J1000', $jenisValidation);

        // Add sample data row
        $sheet->setCellValue('A2', 'joko_susilo');
        $sheet->setCellValue('B2', 'rahasia123');
        $sheet->setCellValue('C2', 'joko@sekolah.sch.id');
        $sheet->setCellValue('D2', 'guru');
        $sheet->setCellValue('E2', 'Joko Susilo');
        $sheet->setCellValue('F2', '198501012010011001');
        $sheet->setCellValue('G2', 'Penata Muda Tk. I/III b');
        $sheet->setCellValue('H2', 'Matematika');
        $sheet->setCellValue('I2', 'PNS');
        $sheet->setCellValue('J2', 'Guru');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(15);

        // Set header style
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ]
            ]
        ];

        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Redirect output to a client's web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_guru.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function processImportGuru()
    {
        // Validate the uploaded file
        $validationRule = [
            'excel_file' => [
                'label' => 'Excel File',
                'rules' => [
                    'uploaded[excel_file]',
                    'ext_in[excel_file,xls,xlsx]',
                    'max_size[excel_file,10240]' // 10MB limit
                ]
            ]
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('excel_file');

        // Process the Excel file
        if ($file->isValid() && !$file->hasMoved()) {
            // Get the temporary file path
            $filePath = $file->getTempName();

            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove the header row
            array_shift($rows);

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            // Start transaction
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                foreach ($rows as $row) {
                    // Check if row is empty
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Extract data from row
                    $username = trim((string) ($row[0] ?? ''));
                    $password = (string) ($row[1] ?? '');
                    $email = trim((string) ($row[2] ?? ''));
                    $role = strtolower(trim((string) ($row[3] ?? '')));
                    $namaLengkap = trim((string) ($row[4] ?? ''));
                    $nip = trim((string) ($row[5] ?? ''));
                    $pangkatGolongan = trim((string) ($row[6] ?? ''));
                    $mataPelajaran = trim((string) ($row[7] ?? ''));
                    $statusKepegawaian = strtoupper(trim((string) ($row[8] ?? '')));
                    $jenisPtk = ucfirst(strtolower(trim((string) ($row[9] ?? 'Guru'))));
                    if ($jenisPtk === '') {
                        $jenisPtk = 'Guru';
                    }

                    // Validate data
                    $errors = [];
                    $allowedRoles = ['guru', 'supervisor', 'kepala', 'admin'];
                    $allowedStatus = ['PNS', 'PPPK', 'GTT', 'PTT', 'Honorer', 'Kontrak'];
                    $allowedJenis = ['Guru', 'Tendik'];

                    // Check if username already exists
                    if (empty($username)) {
                        $errors[] = "Username tidak boleh kosong";
                    } else if ($this->userModel->where('username', $username)->first()) {
                        $errors[] = "Username sudah digunakan";
                    }

                    // Check password length
                    if (strlen($password) < 6) {
                        $errors[] = "Password minimal 6 karakter";
                    }

                    // Check email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Format email tidak valid";
                    }

                    if (!in_array($role, $allowedRoles, true)) {
                        $errors[] = "Role harus salah satu: guru/supervisor/kepala/admin";
                    }
                    if ($statusKepegawaian !== '' && !in_array($statusKepegawaian, $allowedStatus, true)) {
                        $errors[] = "Status kepegawaian harus salah satu: PNS/PPPK/GTT/PTT/Honorer/Kontrak";
                    }
                    if (!in_array($jenisPtk, $allowedJenis, true)) {
                        $errors[] = "Jenis PTK harus Guru atau Tendik";
                    }

                    // Check NIP for PNS/PPPK
                    if (($statusKepegawaian == 'PNS' || $statusKepegawaian == 'PPPK') && strlen($nip) != 18) {
                        $errors[] = "NIP harus 18 digit untuk PNS/PPPK";
                    }

                    // If there are validation errors, skip this row
                    if (!empty($errors)) {
                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => '-',
                            'guru_id' => '-',
                            'keterangan' => implode(', ', $errors)
                        ];
                        $errorCount++;
                        continue;
                    }

                    // Insert into users table
                    $userData = [
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'email' => $email,
                        'role' => $role,
                        'status' => 'Aktif',
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $userId = $this->userModel->insert($userData);

                    if (!$userId) {
                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => '-',
                            'guru_id' => '-',
                            'keterangan' => 'Gagal membuat user'
                        ];
                        $errorCount++;
                        continue;
                    }

                    // Insert into guru table
                    $guruData = [
                        'user_id' => $userId,
                        'nama' => $namaLengkap,
                        'nip' => $nip,
                        'pangkat_golongan' => $pangkatGolongan,
                        'mata_pelajaran' => $mataPelajaran,
                        'mapel_id' => $this->resolveMapelId(null, $mataPelajaran),
                        'jenis_ptk' => $jenisPtk,
                        'status_kepegawaian' => $statusKepegawaian,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $guruId = $this->guruModel->insert($guruData);

                    if (!$guruId) {
                        // Rollback by deleting the user
                        $this->userModel->delete($userId);

                        $results[] = [
                            'username' => $username,
                            'status' => '❌ Error',
                            'user_id' => $userId,
                            'guru_id' => '-',
                            'keterangan' => 'Gagal membuat data guru'
                        ];
                        $errorCount++;
                        continue;
                    }

                    // If role is supervisor, also create supervisor record
                    if ($role == 'supervisor') {
                        $supervisorData = [
                            'user_id' => $userId,
                            'tanggal_penugasan' => date('Y-m-d'),
                            'status' => 'Aktif'
                        ];
                        $this->supervisorModel->insert($supervisorData);
                    }

                    $results[] = [
                        'username' => $username,
                        'status' => '✅ Success',
                        'user_id' => $userId,
                        'guru_id' => $guruId,
                        'keterangan' => 'Berhasil dibuat'
                    ];
                    $successCount++;
                }

                // Complete transaction
                $db->transComplete();

                if ($db->transStatus() === FALSE) {
                    return redirect()->back()->with('error', 'Terjadi kesalahan saat mengimpor data. Silakan coba lagi.');
                }

                return view('admin/pengguna/import_result', [
                    'results' => $results,
                    'successCount' => $successCount,
                    'errorCount' => $errorCount
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'File tidak valid. Silakan coba lagi.');
        }
    }

    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        if ($user) {
            $newStatus = ($user['status'] == 'Aktif') ? 'Non-aktif' : 'Aktif';
            $this->userModel->update($id, ['status' => $newStatus]);

            return redirect()->back()->with('success', 'Status pengguna berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
    }

    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna tidak ditemukan.');
        }

        $newPassword = $this->request->getPost('password') ?: '12345678';

        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);

        return redirect()->back()->with('success', "Password untuk pengguna '{$user['username']}' berhasil direset menjadi: {$newPassword}");
    }

    public function cleanupGuruData()
    {
        // Delete guru records for users who are not actually gurus
        $this->guruModel->where('user_id IS NULL')->delete();

        $users = $this->userModel->findAll();
        foreach ($users as $user) {
            if ($user['role'] != 'guru') {
                $this->guruModel->where('user_id', $user['id'])->delete();
            }
        }

        return "Guru data cleanup completed";
    }

    /**
     * Download Rekapitulasi Akun Pengguna (Username & Password) dalam bentuk Excel (.xlsx)
     * Lengkap dengan styling header, zebra striping, dan border tabel rapi.
     */
    public function exportRekapAkun()
    {
        try {
            $users = $this->userModel
                ->select('users.id, users.username, users.email, users.nip as user_nip, users.role, users.status, users.password, users.last_login, guru.nama as nama_guru, guru.nip as guru_nip, guru.mata_pelajaran, guru.jenis_ptk')
                ->join('guru', 'guru.user_id = users.id', 'left')
                ->orderBy('users.role', 'ASC')
                ->orderBy('users.username', 'ASC')
                ->findAll();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Rekap Akun Pengguna');

            // Page Setup A4 Landscape
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

            // Judul Dokumen (KOP)
            $sheet->setCellValue('A1', 'REKAPITULASI AKUN PENGGUNA (USERNAME & PASSWORD)');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setName('Segoe UI');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $namaSekolah = function_exists('get_nama_sekolah') ? get_nama_sekolah() : 'SISTEM INFORMASI SUPERVISI AKADEMIK';
            $sheet->setCellValue('A2', strtoupper($namaSekolah));
            $sheet->mergeCells('A2:I2');
            $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12)->setName('Segoe UI');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'Tanggal Unduh: ' . date('d F Y, H:i') . ' WIB  |  Total: ' . count($users) . ' Akun Pengguna');
            $sheet->mergeCells('A3:I3');
            $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9.5)->setName('Segoe UI')->getColor()->setARGB('FF64748B');
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Table Header
            $headerRow = 5;
            $sheet->getRowDimension($headerRow)->setRowHeight(28);

            $headers = [
                'A' => 'NO',
                'B' => 'NAMA LENGKAP',
                'C' => 'NIP',
                'D' => 'ROLE / JABATAN',
                'E' => 'USERNAME',
                'F' => 'PASSWORD',
                'G' => 'STATUS PASSWORD',
                'H' => 'EMAIL',
                'I' => 'STATUS AKUN',
            ];

            foreach ($headers as $col => $title) {
                $sheet->setCellValue($col . $headerRow, $title);
            }

            // Header Style
            $headerRange = 'A' . $headerRow . ':I' . $headerRow;
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 10,
                    'name' => 'Segoe UI',
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'], // Navy Blue
                ],
            ]);

            // Table Data Rows
            $currentRow = $headerRow + 1;
            $no = 1;

            foreach ($users as $u) {
                $sheet->getRowDimension($currentRow)->setRowHeight(22);

                $namaLengkap = !empty($u['nama_guru']) ? $u['nama_guru'] : (!empty($u['username']) ? $u['username'] : '-');
                $nip = !empty($u['user_nip']) ? $u['user_nip'] : (!empty($u['guru_nip']) ? $u['guru_nip'] : '-');
                $username = (string) ($u['username'] ?? '-');
                $email = (string) ($u['email'] ?? '-');
                $role = ucfirst((string) ($u['role'] ?? '-'));
                $statusAkun = ucfirst(strtolower((string) ($u['status'] ?? 'Aktif')));

                // Deteksi Password (Default / Custom)
                $passwordHash = (string) ($u['password'] ?? '');
                $passwordText = '12345678';
                $passwordStatus = 'Default';

                if (!empty($passwordHash)) {
                    if (password_verify('admin123', $passwordHash)) {
                        $passwordText = 'admin123';
                        $passwordStatus = 'Default (admin123)';
                    } elseif (password_verify('12345678', $passwordHash)) {
                        $passwordText = '12345678';
                        $passwordStatus = 'Default (12345678)';
                    } elseif (password_verify('123456', $passwordHash)) {
                        $passwordText = '123456';
                        $passwordStatus = 'Default (123456)';
                    } elseif (!empty($u['username']) && password_verify($u['username'], $passwordHash)) {
                        $passwordText = $u['username'];
                        $passwordStatus = 'Sesuai Username';
                    } elseif (!empty($nip) && $nip !== '-' && password_verify($nip, $passwordHash)) {
                        $passwordText = $nip;
                        $passwordStatus = 'Sesuai NIP';
                    } else {
                        // Password telah diubah oleh pengguna menjadi password rahasia pribadi
                        $passwordText = '12345678*';
                        $passwordStatus = 'Telah Diubah Pengguna';
                    }
                }

                $sheet->setCellValue('A' . $currentRow, $no++);
                $sheet->setCellValue('B' . $currentRow, $namaLengkap);
                $sheet->setCellValueExplicit('C' . $currentRow, $nip, DataType::TYPE_STRING);
                $sheet->setCellValue('D' . $currentRow, $role);
                $sheet->setCellValueExplicit('E' . $currentRow, $username, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('F' . $currentRow, $passwordText, DataType::TYPE_STRING);
                $sheet->setCellValue('G' . $currentRow, $passwordStatus);
                $sheet->setCellValue('H' . $currentRow, $email);
                $sheet->setCellValue('I' . $currentRow, $statusAkun);

                // Zebra striping
                if ($no % 2 === 0) {
                    $sheet->getStyle('A' . $currentRow . ':I' . $currentRow)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF8FAFC');
                }

                $currentRow++;
            }

            $lastDataRow = $currentRow - 1;

            // Border Styling untuk Seluruh Tabel (Thin Borders + Medium Outline)
            $tableRange = 'A' . $headerRow . ':I' . $lastDataRow;
            $sheet->getStyle($tableRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'],
                    ],
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['argb' => 'FF1E3A8A'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'font' => [
                    'size' => 10,
                    'name' => 'Segoe UI',
                ],
            ]);

            // Alignment format
            $sheet->getStyle('A' . ($headerRow + 1) . ':A' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . ($headerRow + 1) . ':D' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . ($headerRow + 1) . ':G' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . ($headerRow + 1) . ':I' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Bold styling untuk Nama, Username, dan Password
            $sheet->getStyle('B' . ($headerRow + 1) . ':B' . $lastDataRow)->getFont()->setBold(true);
            $sheet->getStyle('E' . ($headerRow + 1) . ':F' . $lastDataRow)->getFont()->setBold(true);

            // Status Akun Badges
            for ($r = $headerRow + 1; $r <= $lastDataRow; $r++) {
                $statusVal = $sheet->getCell('I' . $r)->getValue();
                if ($statusVal === 'Aktif') {
                    $sheet->getStyle('I' . $r)->getFont()->getColor()->setARGB('FF15803D'); // Dark Green
                    $sheet->getStyle('I' . $r)->getFont()->setBold(true);
                } else {
                    $sheet->getStyle('I' . $r)->getFont()->getColor()->setARGB('FFB91C1C'); // Red
                    $sheet->getStyle('I' . $r)->getFont()->setBold(true);
                }
            }

            // Catatan & Petunjuk Keamanan Akun
            $noteStart = $lastDataRow + 2;
            $sheet->setCellValue('A' . $noteStart, 'Petunjuk & Catatan Keamanan Akun:');
            $sheet->getStyle('A' . $noteStart)->getFont()->setBold(true)->setSize(10)->setName('Segoe UI');

            $sheet->setCellValue('A' . ($noteStart + 1), '1. Password default sistem saat akun dibuat atau direset adalah: 12345678 (atau 123456).');
            $sheet->setCellValue('A' . ($noteStart + 2), '2. Tanda bintang (*) pada kolom Password menandakan pengguna telah mengubah password aslinya. Nilai 12345678 adalah password default jika akun perlu di-reset.');
            $sheet->setCellValue('A' . ($noteStart + 3), '3. Jika pengguna lupa password, Administrator dapat melakukan Reset Password kembali ke 12345678 melalui menu Manajemen Pengguna.');
            $sheet->setCellValue('A' . ($noteStart + 4), '4. Dokumen ini memuat informasi kredensial rahasia. Harap disimpan secara aman dan tidak disebarluaskan secara publik.');

            $sheet->getStyle('A' . ($noteStart + 1) . ':A' . ($noteStart + 4))->getFont()->setSize(9)->setName('Segoe UI')->getColor()->setARGB('FF475569');

            // Auto-size kolom dengan lebar minimum proporsional
            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set lebar kolom ideal
            $minWidths = [
                'A' => 6,
                'B' => 30,
                'C' => 22,
                'D' => 16,
                'E' => 20,
                'F' => 18,
                'G' => 24,
                'H' => 28,
                'I' => 14,
            ];

            foreach ($minWidths as $col => $w) {
                if ($sheet->getColumnDimension($col)->getWidth() < $w) {
                    $sheet->getColumnDimension($col)->setAutoSize(false);
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            }

            // Freeze pane di bawah header tabel
            $sheet->freezePane('A' . ($headerRow + 1));

            // Set HTTP Headers untuk download file Excel
            $filename = 'rekap-akun-pengguna-' . date('Y-m-d') . '.xlsx';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);

            if (ob_get_length()) {
                ob_end_clean();
            }

            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Export Rekap Akun Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendownload rekap akun: ' . $e->getMessage());
        }
    }
}
