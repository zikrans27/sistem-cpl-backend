<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel nilai_detail
 *
 * Menyimpan nilai per-sel tabel penilaian.
 * Setiap baris = satu nilai (angka) untuk satu mahasiswa
 * pada satu kolom komponen (bobotItem) tertentu dalam satu MK.
 *
 * Format nilai_key: "{cpl_index}_{cpmk_index}_{sub_index}_{bobot_index}"
 * Contoh: "0_0_0_1" → CPL[0] → CPMK[0] → SubCPMK[0] → bobotItems[1]
 *
 * Nilai ini digunakan untuk:
 * - Menghitung total per Sub-CPMK  (TabelPenilaianAdmin)
 * - Menghitung capaian CPMK & CPL  (Hasilpengukurancpl)
 * - Ditampilkan ke mahasiswa       (TabelNilaiSaya / CPL Saya)
 */
return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasTable('nilai_detail')) {
        Schema::create('nilai_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')
                  ->constrained('mata_kuliah')
                  ->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')
                  ->constrained('mahasiswa_profiles')
                  ->cascadeOnDelete();
            $table->string('nilai_key', 50)
                  ->comment('Format: {cpl_idx}_{cpmk_idx}_{sub_idx}_{bobot_idx}');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['mata_kuliah_id', 'mahasiswa_id', 'nilai_key'], 'uq_nilai_detail');
        });
    }
}
    public function down(): void
    {
        Schema::dropIfExists('nilai_detail');
    }
};