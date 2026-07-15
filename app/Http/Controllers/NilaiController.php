<?php
namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\MahasiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    // Ambil semua nilai berdasarkan mata kuliah
public function nilaiSaya(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswaProfile;

        if (!$mahasiswa) {
            return response()->json([
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        $nilai = DB::table('nilai_detail as nd')
            ->join('mata_kuliah as mk', 'nd.mata_kuliah_id', '=', 'mk.id')
            ->where('nd.mahasiswa_id', $mahasiswa->id)
            ->select('mk.id as mata_kuliah_id', 'mk.name as mata_kuliah', 'nd.nilai_key', 'nd.nilai')
            ->get()
            ->groupBy('mata_kuliah_id')
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'mata_kuliah_id' => $first->mata_kuliah_id,
                    'mata_kuliah'    => $first->mata_kuliah,
                    'nilai'          => $rows->pluck('nilai', 'nilai_key'),
                ];
            })
            ->values();

        return response()->json($nilai);
    }
    
    public function index(string $mkId)
    {
        $nilai = Nilai::with(['mahasiswa.user'])
            ->where('mata_kuliah', $mkId)
            ->get()
            ->map(fn($n) => [
                'id'          => $n->id,
                'nim'         => $n->mahasiswa->nim,
                'nama'        => $n->mahasiswa->user->name,
                'nilai_angka' => $n->nilai_angka,
                'nilai_huruf' => $n->nilai_huruf,
                'semester'    => $n->semester,
            ]);

        return response()->json($nilai);
    }

    // Simpan atau update nilai
    public function store(Request $request)
    {
        $request->validate([
            'nim'         => 'required|string',
            'mk_id'       => 'required',
            'nilai_angka' => 'nullable|numeric',
            'nilai_huruf' => 'nullable|string',
            'semester'    => 'required|string',
        ]);

        $mahasiswa = MahasiswaProfile::where('nim', $request->nim)->firstOrFail();

        $nilai = Nilai::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'mata_kuliah'  => $request->mk_id,
                'semester'     => $request->semester,
            ],
            [
                'admin_id'    => auth()->id(),
                'nilai_angka' => $request->nilai_angka,
                'nilai_huruf' => $request->nilai_huruf,
            ]
        );

        return response()->json($nilai);
    }

    // Nilai mahasiswa yg sedang login
public function milikSaya(Request $request)
    {
        return $this->nilaiSaya($request);
    }
}