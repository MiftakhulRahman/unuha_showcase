<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nim', 20)->unique();
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('restrict');
            $table->year('angkatan');
            $table->tinyInteger('semester')->default(1);
            $table->string('github_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_mahasiswas');
    }
};
