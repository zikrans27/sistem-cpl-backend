<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'mahasiswa_id',
        'admin_id',
        'mata_kuliah',
        'nilai_angka',
        'nilai_huruf',
        'semester',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaProfile::class, 'mahasiswa_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}