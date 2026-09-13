<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\UserModel;

class AuditLogController extends BaseController
{
    protected $auditLogModel;
    protected $userModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $filters = [
            'user_id' => $this->request->getGet('user_id'),
            'activity_type' => $this->request->getGet('activity_type'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
        ];

        $logs = $this->auditLogModel->getAuditLogsWithUsers($filters)->paginate(20, 'custom_pager');
        $pager = $this->auditLogModel->pager;

        $users = $this->userModel->findAll();

        $data = [
            'logs' => $logs,
            'pager' => $pager,
            'users' => $users,
            'filters' => $filters,
        ];

        return view('admin/laporan/audit_log', $data);
    }

    public function delete($id = null)
    {
        if ($id) {
            $result = $this->auditLogModel->delete($id);

            if ($result) {
                session()->setFlashdata('success', 'Log berhasil dihapus');
            } else {
                session()->setFlashdata('error', 'Gagal menghapus log');
            }
        } else {
            session()->setFlashdata('error', 'ID log tidak ditemukan');
        }

        return redirect()->to(base_url('admin/laporan/audit-log'));
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada log yang dipilih'
            ]);
        }

        try {
            $deletedCount = 0;
            foreach ($ids as $id) {
                if ($this->auditLogModel->delete($id)) {
                    $deletedCount++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Berhasil menghapus ' . $deletedCount . ' log'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
