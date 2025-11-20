<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        Prodi::create([
            'nama' => 'Informatika',
            'kode' => 'IF',
            'deskripsi' => 'Program Studi Informatika',
            'is_active' => true,
        ]);

        Prodi::create([
            'nama' => 'Pendidikan Teknologi Informasi',
            'kode' => 'PTI',
            'deskripsi' => 'Program Studi Pendidikan Teknologi Informasi',
            'is_active' => true,
        ]);
    }
}
