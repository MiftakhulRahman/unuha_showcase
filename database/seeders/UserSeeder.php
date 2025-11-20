<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\ProfileMahasiswa;
use App\Models\ProfileDosen;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $prodiIF = Prodi::where('kode', 'IF')->first();
        $prodiPTI = Prodi::where('kode', 'PTI')->first();

        // Super Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@unuha.ac.id',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'is_active' => true,
            'registration_completed' => true,
        ]);

        // Dosen
        $dosen = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@unuha.ac.id',
            'username' => 'budi_santoso',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'is_active' => true,
            'registration_completed' => true,
        ]);

        ProfileDosen::create([
            'user_id' => $dosen->id,
            'nidn' => '0123456789',
            'prodi_id' => $prodiIF->id,
            'jabatan' => 'Lektor',
            'bidang_keahlian' => 'Web Development & Database',
        ]);

        // Mahasiswa 1
        $mhs1 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'ahmad@student.unuha.ac.id',
            'username' => 'ahmad_wijaya',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'registration_completed' => true,
        ]);

        ProfileMahasiswa::create([
            'user_id' => $mhs1->id,
            'nim' => '21010001',
            'prodi_id' => $prodiIF->id,
            'angkatan' => 2021,
            'semester' => 6,
            'github_url' => 'https://github.com/ahmad-wijaya',
            'linkedin_url' => 'https://linkedin.com/in/ahmad-wijaya',
        ]);

        // Mahasiswa 2
        $mhs2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@student.unuha.ac.id',
            'username' => 'siti_rahma',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'registration_completed' => true,
        ]);

        ProfileMahasiswa::create([
            'user_id' => $mhs2->id,
            'nim' => '21010002',
            'prodi_id' => $prodiPTI->id,
            'angkatan' => 2021,
            'semester' => 6,
            'github_url' => 'https://github.com/siti-rahma',
            'linkedin_url' => 'https://linkedin.com/in/siti-rahma',
        ]);
    }
}
