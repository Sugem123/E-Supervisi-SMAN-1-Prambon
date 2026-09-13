<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SupervisorModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $data['users'] = $this->userModel->findAll();
        return view('admin/pengguna/index', $data);
    }

    public function semua()
    {
        $data['users'] = $this->userModel->findAll();
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
            ->select('guru.*, guru.user_id as account_id, users.username, users.email, guru.is_supervisor')
            ->join('users', 'users.id = guru.user_id', 'left')
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
        return view('admin/pengguna/create');
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[admin,kepala,supervisor,guru]',
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
        $statusValidation->setFormula1('"PNS,PPPK,Honorer"');

        // Apply data validation
        $sheet->setDataValidation('D2:D1000', $roleValidation);
        $sheet->setDataValidation('I2:I1000', $statusValidation);

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

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

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
                    $username = $row[0] ?? '';
                    $password = $row[1] ?? '';
                    $email = $row[2] ?? '';
                    $role = $row[3] ?? '';
                    $namaLengkap = $row[4] ?? '';
                    $nip = $row[5] ?? '';
                    $pangkatGolongan = $row[6] ?? '';
                    $mataPelajaran = $row[7] ?? '';
                    $statusKepegawaian = $row[8] ?? '';

                    // Validate data
                    $errors = [];

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
}
