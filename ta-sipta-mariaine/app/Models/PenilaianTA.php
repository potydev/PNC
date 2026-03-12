<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianTA extends Model
{
    protected $table = 'penilaian_ta';
    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'rubrik_id',
        'jadwal_sidang_tugas_akhir_id',
        'nilai',
        'catatan_revisi',
    ];


    public function rubrik()
    {
        return $this->belongsTo(RubrikNilai::class, 'rubrik_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function jadwalSidang()
    {
        return $this->belongsTo(JadwalSidangTugasAkhir::class, 'jadwal_sidang_tugas_akhir_id');
    }
}
