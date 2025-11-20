<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'active', 'ended'])->default('draft')->index();
            $table->integer('max_participants')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->text('criteria')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['creator_id', 'status']);
            $table->index(['status', 'deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
