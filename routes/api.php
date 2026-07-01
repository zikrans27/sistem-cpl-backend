<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PenilaianController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get(
    '/mahasiswa/nilai-saya',
    [NilaiController::class, 'nilaiSaya']
);

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Mata Kuliah
Route::get('/mata-kuliah', [MataKuliahController::class, 'index']);
Route::get('/mata-kuliah/{id}', [MataKuliahController::class, 'show']);
Route::post('/mata-kuliah', [MataKuliahController::class, 'store']);
Route::put('/mata-kuliah/{id}', [MataKuliahController::class, 'update']);
Route::delete('/mata-kuliah/{id}', [MataKuliahController::class, 'destroy']);

// Nilai
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/nilai/{mkId}', [NilaiController::class, 'index']);
    Route::post('/nilai', [NilaiController::class, 'store']);
    Route::get('/nilai-saya', [NilaiController::class, 'milikSaya']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/penilaian/{mkId}/struktur', [PenilaianController::class, 'getStruktur']);
    Route::post('/penilaian/{mkId}/struktur', [PenilaianController::class, 'simpanStruktur']);
    Route::get('/penilaian/{mkId}/nilai', [PenilaianController::class, 'getNilai']);
    Route::post('/penilaian/{mkId}/nilai', [PenilaianController::class, 'simpanNilai']);
});