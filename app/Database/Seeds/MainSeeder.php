<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('TahunAjarSeeder');
        $this->call('UsersSeeder');
        $this->call('RefMapelSeeder');
        $this->call('GuruSeeder');
        $this->call('SupervisorSeeder');
        $this->call('JenisPenilaianSeeder');
        $this->call('KelasSeeder');
        $this->call('AspekPenilaianSeeder');
        $this->call('JadwalSupervisiSeeder');
        $this->call('HasilSupervisiSeeder');
        $this->call('DetailHasilPenilaianSeeder');
        $this->call('DokumenAjarSeeder');
        $this->call('FotoBuktiSeeder');
        $this->call('AuditLogsSeeder');
        $this->call('SystemSettingsSeeder');
        $this->call('NotifikasiSeeder');
        $this->call('UserPreferencesSeeder');
    }
}
