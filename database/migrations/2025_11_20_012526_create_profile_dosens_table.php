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
        Schema::create('profile_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nidn', 20)->unique();
            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('restrict');
            $table->string('jabatan', 100)->nullable();
            $table->string('bidang_keahlian', 255)->nullable();
            $table->string('scholar_url', 255)->nullable();
            $table->string('scopus_url', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_dosens');
    }
};
