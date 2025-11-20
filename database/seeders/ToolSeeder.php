<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            ['nama' => 'Laravel', 'slug' => 'laravel', 'icon' => '🔴', 'color' => 'red'],
            ['nama' => 'Vue.js', 'slug' => 'vuejs', 'icon' => '💚', 'color' => 'green'],
            ['nama' => 'React', 'slug' => 'react', 'icon' => '⚛️', 'color' => 'blue'],
            ['nama' => 'Next.js', 'slug' => 'nextjs', 'icon' => '⬛', 'color' => 'black'],
            ['nama' => 'TypeScript', 'slug' => 'typescript', 'icon' => '🔵', 'color' => 'blue'],
            ['nama' => 'Python', 'slug' => 'python', 'icon' => '🐍', 'color' => 'yellow'],
            ['nama' => 'JavaScript', 'slug' => 'javascript', 'icon' => '⚡', 'color' => 'yellow'],
            ['nama' => 'Node.js', 'slug' => 'nodejs', 'icon' => '💚', 'color' => 'green'],
            ['nama' => 'MongoDB', 'slug' => 'mongodb', 'icon' => '🍃', 'color' => 'green'],
            ['nama' => 'PostgreSQL', 'slug' => 'postgresql', 'icon' => '🐘', 'color' => 'blue'],
            ['nama' => 'MySQL', 'slug' => 'mysql', 'icon' => '🐬', 'color' => 'blue'],
            ['nama' => 'Docker', 'slug' => 'docker', 'icon' => '🐳', 'color' => 'blue'],
            ['nama' => 'Git', 'slug' => 'git', 'icon' => '🔄', 'color' => 'orange'],
            ['nama' => 'AWS', 'slug' => 'aws', 'icon' => '☁️', 'color' => 'orange'],
            ['nama' => 'Firebase', 'slug' => 'firebase', 'icon' => '🔥', 'color' => 'orange'],
            ['nama' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'icon' => '💨', 'color' => 'cyan'],
            ['nama' => 'Bootstrap', 'slug' => 'bootstrap', 'icon' => '📦', 'color' => 'purple'],
            ['nama' => 'Figma', 'slug' => 'figma', 'icon' => '🎨', 'color' => 'purple'],
        ];

        foreach ($tools as $tool) {
            Tool::create([
                'nama' => $tool['nama'],
                'slug' => $tool['slug'],
                'icon' => $tool['icon'],
                'color' => $tool['color'],
                'deskripsi' => $tool['nama'],
                'is_active' => true,
            ]);
        }
    }
}
