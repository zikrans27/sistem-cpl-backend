<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasTable('mk_mahasiswa')) {
        Schema::create('mk_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')
                  ->constrained('mata_kuliah')
                  ->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')
                  ->constrained('mahasiswa_profiles')
                  ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'mahasiswa_id']);
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('mk_mahasiswa');
    }

    
};