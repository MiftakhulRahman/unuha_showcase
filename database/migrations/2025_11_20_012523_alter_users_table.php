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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 100)->unique()->after('email');
            $table->string('avatar', 255)->nullable()->after('password');
            $table->text('bio')->nullable()->after('avatar');
            $table->enum('role', ['superadmin', 'dosen', 'mahasiswa'])->default('mahasiswa')->after('bio');
            $table->boolean('is_active')->default(true)->after('role');
            $table->boolean('registration_completed')->default(false)->after('is_active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn(['username', 'avatar', 'bio', 'role', 'is_active', 'registration_completed']);
            }
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
