<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UpdateKepalaSekolahData extends Seeder
{
    public function run()
    {
        $data = [
            'nip' => '19750817 200003 1 008',
        ];

        // Update kepala sekolah data
        $this->db->table('users')
                 ->where('username', 'Sipulloh')
                 ->update($data);
    }
}