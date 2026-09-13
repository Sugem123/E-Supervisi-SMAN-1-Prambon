<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\JadwalSupervisiModel;
use App\Models\GuruModel;

class CheckJadwalMataPelajaran extends BaseCommand
{
    protected $group = 'App';
    protected $name = 'app:check-jadwal-mata';
    protected $description = 'Check jadwal_supervisi.mata_pelajaran for suspicious values (e.g. values like "Guru kelas ...")';

    public function run(array $params)
    {
        $jadwalModel = new JadwalSupervisiModel();
        $guruModel = new GuruModel();

        CLI::write('Scanning jadwal_supervisi for suspicious mata_pelajaran...', 'yellow');

        // Find rows where mata_pelajaran starts with "Guru " (common wrong value in dataset)
        $builder = $jadwalModel->builder();
        $builder->select('jadwal_supervisi.id, jadwal_supervisi.guru_id, jadwal_supervisi.mata_pelajaran as jadwal_mp, guru.mata_pelajaran as guru_mp, jadwal_supervisi.tanggal_supervisi');
        $builder->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left');
        $builder->where("jadwal_supervisi.mata_pelajaran REGEXP '^Guru\\s+'");

        $results = $builder->get()->getResultArray();

        if (empty($results)) {
            CLI::write('No jadwal_supervisi entries matching /^Guru\\s+/ found.', 'green');
        } else {
            CLI::write('Found ' . count($results) . ' suspicious rows:', 'red');
            foreach ($results as $row) {
                CLI::write(sprintf("- id=%s, guru_id=%s, tanggal=%s", $row['id'], $row['guru_id'], $row['tanggal_supervisi']), 'yellow');
                CLI::write('  jadwal.mata_pelajaran = ' . ($row['jadwal_mp'] ?? '(null)'));
                CLI::write('  guru.mata_pelajaran   = ' . ($row['guru_mp'] ?? '(null)'));
            }
        }

        // Additional check: rows where jadwal_mp equals guru_mp (possible copy)
        CLI::write('\nChecking rows where jadwal.mata_pelajaran equals guru.mata_pelajaran...', 'yellow');
        $builder2 = $jadwalModel->builder();
        $builder2->select('jadwal_supervisi.id, jadwal_supervisi.guru_id, jadwal_supervisi.mata_pelajaran as jadwal_mp, guru.mata_pelajaran as guru_mp, jadwal_supervisi.tanggal_supervisi')
                 ->join('guru', 'guru.id = jadwal_supervisi.guru_id', 'left')
                 ->where('jadwal_supervisi.mata_pelajaran = guru.mata_pelajaran');

        $res2 = $builder2->get()->getResultArray();

        if (empty($res2)) {
            CLI::write('No rows found where jadwal.mata_pelajaran equals guru.mata_pelajaran.', 'green');
        } else {
            CLI::write('Found ' . count($res2) . ' rows where jadwal_mp == guru_mp (possible copied values):', 'red');
            foreach ($res2 as $row) {
                CLI::write(sprintf("- id=%s, guru_id=%s, tanggal=%s", $row['id'], $row['guru_id'], $row['tanggal_supervisi']), 'yellow');
                CLI::write('  jadwal.mata_pelajaran = ' . ($row['jadwal_mp'] ?? '(null)'));
                CLI::write('  guru.mata_pelajaran   = ' . ($row['guru_mp'] ?? '(null)'));
            }
        }

        CLI::write('\nScan complete. Review the listed rows and fix manually or re-run with an updater (not implemented by this command).', 'green');
    }
}
