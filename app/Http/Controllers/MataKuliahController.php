<?php
namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    // Ambil semua mata kuliah
    protected $fillable = ['name', 'tipe', 'tahun', 'semester', 'color'];
    public function index()
    {
        $colors = ['c1','c2','c3','c4','c5','c6','c7','c8','c9','c10'];
        $list = MataKuliah::all()->map(function ($mk, $i) use ($colors) {
        $mk->color = $colors[$i % count($colors)];
        return $mk;
    });
        return response()->json($list);
    }

    // Ambil satu mata kuliah
    public function show(string $id)
    {
        return response()->json(MataKuliah::findOrFail($id));
    }

    // Tambah
public function store(Request $request)
{
    $data = $request->only(['name', 'tipe', 'tahun']);
    $data['semester'] = $data['tipe'] === 'ganjil' ? 'Semester Ganjil' : 'Semester Genap';
    $data['color'] = 'c' . ((MataKuliah::count() % 10) + 1);

    $mk = MataKuliah::create($request->only(['name', 'tipe', 'tahun']));
    return response()->json($mk, 201);
}

// Edit
public function update(Request $request, string $id)
{
    $mk = MataKuliah::findOrFail($id);

    $data = $request->only(['name', 'tipe', 'tahun']);
    $data['semester'] = $data['tipe'] === 'ganjil' ? 'Semester Ganjil' : 'Semester Genap';
    
    $mk->update($request->only(['name', 'tipe', 'tahun']));
    return response()->json($mk);
}

// Hapus
public function destroy(string $id)
{
    MataKuliah::findOrFail($id)->delete();
    return response()->json(null, 204);
}
}