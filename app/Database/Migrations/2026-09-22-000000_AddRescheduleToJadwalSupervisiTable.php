<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRescheduleToJadwalSupervisiTable extends Migration
{
    public function up()
    {
        $existingFields = $this->db->getFieldNames('jadwal_supervisi');
        $fields = [];

        if (!in_array('status_ajuan', $existingFields, true)) {
            $fields['status_ajuan'] = [
                'type'       => "ENUM('Tidak Ada','Diajukan','Disetujui','Ditolak')",
                'default'    => 'Tidak Ada',
                'null'       => false,
                'after'      => 'status',
            ];
        }

        if (!in_array('alasan_batal', $existingFields, true)) {
            $fields['alasan_batal'] = [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status_ajuan',
            ];
        }

        if (!in_array('usulan_tanggal', $existingFields, true)) {
            $fields['usulan_tanggal'] = [
                'type'       => 'DATE',
                'null'       => true,
                'after'      => 'alasan_batal',
            ];
        }

        if (!in_array('usulan_hari', $existingFields, true)) {
            $fields['usulan_hari'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'usulan_tanggal',
            ];
        }

        if (!in_array('usulan_jam_ke', $existingFields, true)) {
            $fields['usulan_jam_ke'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'usulan_hari',
            ];
        }

        if (!in_array('usulan_waktu_dari', $existingFields, true)) {
            $fields['usulan_waktu_dari'] = [
                'type'       => 'TIME',
                'null'       => true,
                'after'      => 'usulan_jam_ke',
            ];
        }

        if (!in_array('usulan_waktu_sampai', $existingFields, true)) {
            $fields['usulan_waktu_sampai'] = [
                'type'       => 'TIME',
                'null'       => true,
                'after'      => 'usulan_waktu_dari',
            ];
        }

        if (!in_array('usulan_kelas_id', $existingFields, true)) {
            $fields['usulan_kelas_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'usulan_waktu_sampai',
            ];
        }

        if (!in_array('usulan_kelas', $existingFields, true)) {
            $fields['usulan_kelas'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'usulan_kelas_id',
            ];
        }

        if (!in_array('catatan_supervisor', $existingFields, true)) {
            $fields['catatan_supervisor'] = [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'usulan_kelas',
            ];
        }

        if (!in_array('tgl_respon_supervisor', $existingFields, true)) {
            $fields['tgl_respon_supervisor'] = [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'catatan_supervisor',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('jadwal_supervisi', $fields);
        }
    }

    public function down()
    {
        $existingFields = $this->db->getFieldNames('jadwal_supervisi');
        $columns = [
            'status_ajuan',
            'alasan_batal',
            'usulan_tanggal',
            'usulan_hari',
            'usulan_jam_ke',
            'usulan_waktu_dari',
            'usulan_waktu_sampai',
            'usulan_kelas_id',
            'usulan_kelas',
            'catatan_supervisor',
            'tgl_respon_supervisor',
        ];

        foreach ($columns as $col) {
            if (in_array($col, $existingFields, true)) {
                $this->forge->dropColumn('jadwal_supervisi', $col);
            }
        }
    }
}
