<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\Database\Forge;

class BackupController extends BaseController
{
    protected $db;
    protected $dbforge;
    protected $backupPath;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->dbforge = \Config\Database::forge();
        $this->backupPath = WRITEPATH . 'backups/';

        // Create backup directory if it doesn't exist
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    public function index()
    {
        $data['backups'] = $this->listBackups();
        $data['title'] = 'Backup & Restore Database';

        return view('admin/backup/index', $data);
    }

    public function backup()
    {
        try {
            $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
            $filepath = $this->backupPath . $filename;

            // Custom backup implementation (platform-independent)
            $sqlDump = $this->generateSQLDump();

            // Write backup to file
            file_put_contents($filepath, $sqlDump);

            // Compress the backup
            $zipFilename = $filename . '.zip';
            $zipFilepath = $this->backupPath . $zipFilename;

            $zip = new \ZipArchive();
            if ($zip->open($zipFilepath, \ZipArchive::CREATE) === TRUE) {
                $zip->addFile($filepath, $filename);
                $zip->close();

                // Delete the uncompressed SQL file
                unlink($filepath);
            } else {
                throw new \Exception('Gagal membuat file ZIP');
            }

            // Save backup metadata
            $metadata = [
                'filename' => $zipFilename,
                'size' => filesize($zipFilepath),
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => session()->get('username')
            ];

            $metadataFile = $this->backupPath . 'metadata.json';
            $allMetadata = [];

            if (file_exists($metadataFile)) {
                $allMetadata = json_decode(file_get_contents($metadataFile), true);
            }

            $allMetadata[] = $metadata;
            file_put_contents($metadataFile, json_encode($allMetadata, JSON_PRETTY_PRINT));

            return redirect()->to('/admin/backup')->with('success', 'Backup berhasil dibuat: ' . $zipFilename);
        } catch (\Exception $e) {
            log_message('error', 'Backup error: ' . $e->getMessage());
            return redirect()->to('/admin/backup')->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function restore()
    {
        $file = $this->request->getFile('backup_file');

        if (!$file || !$file->isValid()) {
            return redirect()->to('/admin/backup')->with('error', 'File backup tidak valid atau gagal diunggah.');
        }

        // Validate file extension
        $extension = strtolower($file->getExtension());
        $allowedExtensions = ['sql', 'zip'];
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->to('/admin/backup')->with('error', 'File harus berformat .sql atau .zip');
        }

        $tempPath = WRITEPATH . 'temp/';
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $filename = $file->getRandomName();
        $filepath = $tempPath . $filename;
        $extractedFiles = [];

        try {
            $file->move($tempPath, $filename);
            $extractedFiles[] = $filepath;

            $sqlContent = '';

            if ($extension === 'zip') {
                $zip = new \ZipArchive();
                if ($zip->open($filepath) === TRUE) {
                    $sqlEntryName = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entryName = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($entryName, PATHINFO_EXTENSION)) === 'sql') {
                            $sqlEntryName = $entryName;
                            break;
                        }
                    }

                    if (!$sqlEntryName) {
                        $zip->close();
                        throw new \Exception('Arsip ZIP tidak memuat berkas berekstensi .sql di dalamnya.');
                    }

                    $sqlContent = $zip->getFromName($sqlEntryName);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal mengekstrak arsip ZIP cadangan.');
                }
            } else {
                $sqlContent = file_get_contents($filepath);
            }

            // Cleanup temp files immediately
            foreach ($extractedFiles as $f) {
                if (file_exists($f)) {
                    @unlink($f);
                }
            }

            $result = $this->executeSQLDump($sqlContent);

            if (!$result['success']) {
                return redirect()->to('/admin/backup')->with('error', 'Pemulihan gagal: ' . $result['error']);
            }

            return redirect()->to('/admin/backup')->with('success', 'Database berhasil dipulihkan! Sebanyak ' . $result['executed'] . ' perintah SQL berhasil dijalankan.');
        } catch (\Throwable $e) {
            foreach ($extractedFiles as $f) {
                if (file_exists($f)) {
                    @unlink($f);
                }
            }
            log_message('error', 'Restore error: ' . $e->getMessage());
            return redirect()->to('/admin/backup')->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filepath = $this->backupPath . $filename;

        if (!file_exists($filepath)) {
            return redirect()->to('/admin/backup')->with('error', 'File backup tidak ditemukan');
        }

        return $this->response->download($filepath, null);
    }

    public function delete($filename)
    {
        $filepath = $this->backupPath . $filename;

        if (!file_exists($filepath)) {
            return redirect()->to('/admin/backup')->with('error', 'File backup tidak ditemukan');
        }

        try {
            unlink($filepath);

            // Update metadata
            $metadataFile = $this->backupPath . 'metadata.json';
            if (file_exists($metadataFile)) {
                $allMetadata = json_decode(file_get_contents($metadataFile), true);
                $allMetadata = array_filter($allMetadata, function ($m) use ($filename) {
                    return $m['filename'] !== $filename;
                });
                file_put_contents($metadataFile, json_encode(array_values($allMetadata), JSON_PRETTY_PRINT));
            }

            return redirect()->to('/admin/backup')->with('success', 'Backup berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/admin/backup')->with('error', 'Gagal menghapus backup: ' . $e->getMessage());
        }
    }

    /**
     * Restore database directly from an existing backup file on server
     */
    public function restoreExisting($filename)
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $filepath = $this->backupPath . $filename;

        if (!file_exists($filepath)) {
            return redirect()->to('/admin/backup')->with('error', 'Berkas cadangan tidak ditemukan di server.');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedExtensions = ['sql', 'zip'];
        if (!in_array($extension, $allowedExtensions)) {
            return redirect()->to('/admin/backup')->with('error', 'Format berkas cadangan tidak didukung.');
        }

        try {
            $sqlContent = '';

            if ($extension === 'zip') {
                $zip = new \ZipArchive();
                if ($zip->open($filepath) === TRUE) {
                    $sqlEntryName = null;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entryName = $zip->getNameIndex($i);
                        if (strtolower(pathinfo($entryName, PATHINFO_EXTENSION)) === 'sql') {
                            $sqlEntryName = $entryName;
                            break;
                        }
                    }

                    if (!$sqlEntryName) {
                        $zip->close();
                        throw new \Exception('Arsip ZIP tidak memuat berkas berekstensi .sql di dalamnya.');
                    }

                    $sqlContent = $zip->getFromName($sqlEntryName);
                    $zip->close();
                } else {
                    throw new \Exception('Gagal membuka arsip berkas cadangan ZIP.');
                }
            } else {
                $sqlContent = file_get_contents($filepath);
            }

            $result = $this->executeSQLDump($sqlContent);

            if (!$result['success']) {
                return redirect()->to('/admin/backup')->with('error', 'Pemulihan dari arsip ' . esc($filename) . ' gagal: ' . $result['error']);
            }

            return redirect()->to('/admin/backup')->with('success', 'Database berhasil dipulihkan dari arsip "' . esc($filename) . '"! Sebanyak ' . $result['executed'] . ' query berhasil dijalankan.');
        } catch (\Throwable $e) {
            log_message('error', 'Restore existing error: ' . $e->getMessage());
            return redirect()->to('/admin/backup')->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Parse and execute SQL dump safely
     *
     * @param string $sql
     * @return array ['success' => bool, 'executed' => int, 'error' => string|null]
     */
    private function executeSQLDump(string $sql): array
    {
        @set_time_limit(300);

        if (trim($sql) === '') {
            return [
                'success' => false,
                'executed' => 0,
                'error' => 'File SQL kosong atau tidak memiliki query yang dapat dieksekusi.'
            ];
        }

        // Parse SQL queries
        $queries = [];
        $query = '';
        $in_string = false;
        $esc = false;
        $len = strlen($sql);

        for ($i = 0; $i < $len; $i++) {
            $char = $sql[$i];

            if ($in_string) {
                if ($char === '\\') {
                    $esc = !$esc;
                } elseif ($char === "'" && !$esc) {
                    $in_string = false;
                } else {
                    $esc = false;
                }
            } else {
                if ($char === "'") {
                    $in_string = true;
                }
            }

            if ($char === ';' && !$in_string) {
                $trimmed = trim($query);
                if ($trimmed !== '') {
                    $queries[] = $trimmed;
                }
                $query = '';
            } else {
                $query .= $char;
            }
        }

        $trimmed = trim($query);
        if ($trimmed !== '') {
            $queries[] = $trimmed;
        }

        if (empty($queries)) {
            return [
                'success' => false,
                'executed' => 0,
                'error' => 'Tidak ada query SQL valid yang ditemukan dalam berkas.'
            ];
        }

        // Disable foreign key checks prior to execution
        $this->db->simpleQuery('SET FOREIGN_KEY_CHECKS=0;');

        $this->db->transStart();
        $executedCount = 0;

        try {
            foreach ($queries as $i => $q) {
                // Filter out queries that only contain comments or whitespace
                $lines = explode("\n", $q);
                $meaningful = '';
                foreach ($lines as $line) {
                    $trimmedLine = trim($line);
                    if ($trimmedLine === '' || str_starts_with($trimmedLine, '--') || str_starts_with($trimmedLine, '#') || str_starts_with($trimmedLine, '/*')) {
                        continue;
                    }
                    $meaningful .= $line . "\n";
                }

                if (trim($meaningful) === '') {
                    continue; // Skip pure comment block
                }

                if (!$this->db->simpleQuery($q)) {
                    $error = $this->db->error();
                    $querySnippet = strlen($q) > 100 ? substr($q, 0, 100) . '...' : $q;
                    throw new \Exception("Gagal pada query ke-" . ($i + 1) . ": " . ($error['message'] ?? 'Unknown database error') . " | SQL: " . $querySnippet);
                }
                $executedCount++;
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                $error = $this->db->error();
                throw new \Exception('Transaksi restore gagal: ' . ($error['message'] ?? 'Database transaction error'));
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            $this->db->simpleQuery('SET FOREIGN_KEY_CHECKS=1;');
            return [
                'success' => false,
                'executed' => $executedCount,
                'error' => $e->getMessage()
            ];
        }

        // Re-enable foreign key checks
        $this->db->simpleQuery('SET FOREIGN_KEY_CHECKS=1;');

        return [
            'success' => true,
            'executed' => $executedCount,
            'error' => null
        ];
    }

    /**
     * Generate SQL dump manually
     */
    private function generateSQLDump()
    {
        $sqlDump = "-- Database Backup\n";
        $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sqlDump .= "-- Database: " . $this->db->database . "\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Get all tables
        $tables = $this->db->listTables();

        foreach ($tables as $table) {
            $sqlDump .= "-- --------------------------------------------------------\n";
            $sqlDump .= "-- Table structure for table `{$table}`\n";
            $sqlDump .= "-- --------------------------------------------------------\n\n";

            // Drop table if exists
            $sqlDump .= "DROP TABLE IF EXISTS `{$table}`;\n\n";

            // Get CREATE TABLE statement
            $query = $this->db->query("SHOW CREATE TABLE `{$table}`");
            $row = $query->getRow();
            $createTable = $row->{'Create Table'};
            $sqlDump .= $createTable . ";\n\n";

            // Get table data
            $query = $this->db->query("SELECT * FROM `{$table}`");
            $results = $query->getResult();

            if (!empty($results)) {
                $sqlDump .= "-- Dumping data for table `{$table}`\n\n";

                foreach ($results as $row) {
                    $values = [];
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . $this->db->escapeString($value) . "'";
                        }
                    }
                    $sqlDump .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlDump .= "\n";
            }
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sqlDump;
    }

    private function listBackups()
    {
        $backups = [];
        $metadataFile = $this->backupPath . 'metadata.json';

        if (file_exists($metadataFile)) {
            $backups = json_decode(file_get_contents($metadataFile), true);

            // Sort by created_at descending
            usort($backups, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return $backups;
    }
}
