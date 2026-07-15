<?php
namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\MahasiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    // Simpan struktur CPL/CPMK/Sub-CPMK
    public function simpanStruktur(Request $request, $mkId)
    {
        DB::table('penilaian_struktur')->updateOrInsert(
            ['mata_kuliah_id' => $mkId],
            [
                'struktur'    => json_encode($request->struktur),
                'updated_at'  => now(),
                'created_at'  => now(),
            ]
        );
        return response()->json(['message' => 'Struktur tersimpan']);
    }

    // Ambil struktur CPL/CPMK/Sub-CPMK
    public function getStruktur($mkId)
    {
        $struktur = DB::table('penilaian_struktur')
            ->where('mata_kuliah_id', $mkId)
            ->first();

        return response()->json([
            'struktur' => $struktur ? json_decode($struktur->struktur) : null
        ]);
    }

    // Simpan nilai mahasiswa
// Simpan nilai mahasiswa
public function simpanNilai(Request $request, $mkId)
{
    $request->validate([
        'mahasiswa' => 'required|array',
    ]);

    foreach ($request->mahasiswa as $mhs) {
        if (empty($mhs['nim'])) continue;

        $mahasiswa = MahasiswaProfile::where('nim', $mhs['nim'])->first();
        if (!$mahasiswa) continue;

        foreach ($mhs['nilai'] as $key => $angka) {
            if ($angka === '' || $angka === null) continue;

            DB::table('nilai_detail')->updateOrInsert(
                [
                    'mata_kuliah_id' => $mkId,
                    'mahasiswa_id'   => $mahasiswa->id,
                    'nilai_key'      => $key,
                ],
                [
                    'nilai'      => $angka,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    return response()->json(['message' => 'Nilai tersimpan']);
}

// Ambil semua nilai per mata kuliah
public function getNilai($mkId)
{
    $nilaiList = DB::table('nilai_detail as nd')
        ->join('mahasiswa_profiles as mp', 'nd.mahasiswa_id', '=', 'mp.id')
        ->join('users as u', 'mp.user_id', '=', 'u.id')
        ->where('nd.mata_kuliah_id', $mkId)
        ->select('u.name as nama', 'mp.nim', 'nd.nilai_key', 'nd.nilai as nilai_angka')
        ->get();

    // Group by NIM
    $grouped = [];
    foreach ($nilaiList as $n) {
        if (!isset($grouped[$n->nim])) {
            $grouped[$n->nim] = [
                'nim'   => $n->nim,
                'nama'  => $n->nama,
                'nilai' => [],
            ];
        }
        $grouped[$n->nim]['nilai'][$n->nilai_key] = $n->nilai_angka;
    }

    return response()->json(array_values($grouped));
}
}