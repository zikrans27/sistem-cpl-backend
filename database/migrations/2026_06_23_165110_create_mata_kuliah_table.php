<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mata_kuliah')) {
            Schema::create('mata_kuliah', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('tipe')->nullable()->comment('ganjil | genap');
                $table->string('semester')->nullable();
                $table->string('tahun')->nullable()->comment('Contoh: 2024/2025');
                $table->string('color')->default('c1')->comment('Kode warna tema kartu MK');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};