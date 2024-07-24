<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Akunting\JenisPeralatan;
use App\Models\User;
use App\Models\Akunting\ParamPeralatan;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // seeder data role
        $role = ['admin', 'admin-head', 'akunting', 'it', 'finance', 'atasan_akunting', 'pengurus', 'pengawas'];
        foreach ($role as $r) {
            Role::create(['name' => $r]);
        }

        // sedeer data user
        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'Admin',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'Admin',
            'email' => 'admin@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('admin');

        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'IT',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'IT',
            'email' => 'it@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('it');

        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'Akunting',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'Akunting',
            'email' => 'akunting@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('akunting');

        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'Admin Head',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'Admin Head',
            'email' => 'ah@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('admin-head');

        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'Pengurus',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'Pengurus',
            'email' => 'pengurus@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('pengurus');

        $user = User::create([
            'kode_branch' => 001,
            'code_area' => 1101,
            'name' => 'Finance',
            'nik' => 2305101002,
            'nama_bank' => 'BSI',
            'no_rek' => 7103686247,
            'atas_nama' => 'Finance',
            'email' => 'finance@ni',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        
        $user->generate_username();
        $user->assignRole('finance');

        // seeder data peralatan
        ParamPeralatan::create([
            'coa' => '55511',
            'nama' => 'BBJ-Peralatan-Kantor',
        ]);

        ParamPeralatan::create([
            'coa' => '55512',
            'nama' => 'BBJ-Peralatan-Dapur',
        ]);

        ParamPeralatan::create([
            'coa' => '55512',
            'nama' => 'BBJ-Peralatan-Rumah Tangga',
        ]);

        ParamPeralatan::create([
            'coa' => '55512',
            'nama' => 'BBJ-Peralatan-Kecil',
        ]);

        JenisPeralatan::create([
            'param_peralatan_id' => 1,
            'nama_jenis' => 'SSD',
            'group_approval' => 'it'
        ]);

        JenisPeralatan::create([
            'param_peralatan_id' => 1,
            'nama_jenis' => 'Kalkulator',
            'group_approval' => 'umum'
        ]);
    }
}
