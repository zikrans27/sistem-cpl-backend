<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel penilaian_struktur
 *
 * Menyimpan struktur CPL / CPMK / Sub-CPMK setiap mata kuliah dalam bentuk JSON.
 *
 * Struktur JSON di kolom 'struktur':
 * {
 *   "cplList": [
 *     {
 *       "name": "CPL-1",
 *       "cpmkList": [
 *         {
 *           "name": "CPMK-1",
 *           "persen": 50,
 *           "subCpmkList": [
 *             {
 *               "name": "Sub-CPMK-1",
 *               "totalBobot": 100,
 *               "standar": 80,
 *               "bobotItems": [
 *                 { "label": "UTS", "bobot": 40 },
 *                 { "label": "UAS", "bobot": 60 }
 *               ]
 *             }
 *           ]
 *         }
 *       ]
 *     }
 *   ],
 *   "kategoriCPMK": { "k_0_0": "Kognitif" },
 *   "kategoriCPL":  { "kc_0": "Sikap" },
 *   "standarCPL":   { "sc_0": 80 }
 * }
 *
 * nilaiKey di nilai_detail: "{cpl_index}_{cpmk_index}_{sub_index}_{bobot_index}"
 * Contoh: "0_0_0_1" → CPL-1 → CPMK-1 → Sub-CPMK-1 → bobotItems[1] (UAS)
 */
return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasTable('penilaian_struktur')) {
        Schema::create('penilaian_struktur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_kuliah_id')
                  ->unique()
                  ->constrained('mata_kuliah')
                  ->cascadeOnDelete();
            $table->json('struktur')->nullable();
            $table->timestamps();
        });
    }
}

    public function down(): void
    {
        Schema::dropIfExists('penilaian_struktur');
    }
};