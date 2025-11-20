<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Web Application', 'slug' => 'web-application', 'icon' => '🌐'],
            ['nama' => 'Mobile App', 'slug' => 'mobile-app', 'icon' => '📱'],
            ['nama' => 'Desktop Application', 'slug' => 'desktop-application', 'icon' => '💻'],
            ['nama' => 'Machine Learning', 'slug' => 'machine-learning', 'icon' => '🤖'],
            ['nama' => 'Data Visualization', 'slug' => 'data-visualization', 'icon' => '📊'],
            ['nama' => 'Game Development', 'slug' => 'game-development', 'icon' => '🎮'],
            ['nama' => 'API & Backend', 'slug' => 'api-backend', 'icon' => '⚙️'],
            ['nama' => 'Frontend', 'slug' => 'frontend', 'icon' => '✨'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create([
                'nama' => $kategori['nama'],
                'slug' => $kategori['slug'],
                'icon' => $kategori['icon'],
                'deskripsi' => ucfirst($kategori['slug']),
                'is_active' => true,
            ]);
        }
    }
}
