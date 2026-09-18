<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RefMapelSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        if ($this->db->tableExists('ref_mapel')) {
            $this->db->table('ref_mapel')->truncate();
        }

        $mapels = [
            ['Pendidikan Agama dan Budi Pekerti', 'A', 'Aktif'],
            ['Pendidikan Pancasila', 'A', 'Aktif'],
            ['Bahasa Indonesia', 'A', 'Aktif'],
            ['Matematika', 'A', 'Aktif'],
            ['Sejarah Indonesia', 'A', 'Aktif'],
            ['Bahasa Inggris', 'A', 'Aktif'],
            ['Seni dan Budaya', 'B', 'Aktif'],
            ['Pendidikan Jasmani, Olahraga, dan Kesehatan', 'B', 'Aktif'],
            ['Prakarya dan Kewirausahaan', 'B', 'Aktif'],
            ['Muatan Lokal', 'B', 'Aktif'],
            ['Matematika (Peminatan)', 'C', 'Aktif'],
            ['Biologi', 'C', 'Aktif'],
            ['Fisika', 'C', 'Aktif'],
            ['Kimia', 'C', 'Aktif'],
            ['Informatika', 'C', 'Aktif'],
            ['Geografi', 'C', 'Aktif'],
            ['Sejarah (Peminatan)', 'C', 'Aktif'],
            ['Sosiologi', 'C', 'Aktif'],
            ['Ekonomi', 'C', 'Aktif'],
            ['Bahasa dan Sastra Indonesia (Peminatan)', 'C', 'Aktif'],
            ['Bahasa dan Sastra Inggris (Peminatan)', 'C', 'Aktif'],
            ['Bahasa Asing Lainnya', 'C', 'Aktif'],
        ];

        $builder = $this->db->table('ref_mapel');
        foreach ($mapels as $index => $mapel) {
            $builder->insert([
                'id' => $index + 1,
                'nama_mapel' => $mapel[0],
                'jenjang' => 'SMA',
                'kelompok' => $mapel[1],
                'status' => $mapel[2],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->db->enableForeignKeyChecks();
    }
}
