<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSidang extends Model
{
    use HasFactory;

    protected $table = 'hasil_sidang';

    protected $fillable = [
        'mahasiswa_id',
        'status_kelulusan',
        'tahun_lulus',
        'file_revisi',
        'tanggal_revisi',
        'kelengkapan_yudisium',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function riwayatSidang()
    {
        return $this->hasMany(RiwayatSidang::class);
    }
}
