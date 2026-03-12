<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatatanRevisiTA extends Model
{
    protected $table = 'catatan_revisi_ta';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'jadwal_sidang_tugas_akhir_id',
        'catatan_revisi',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function jadwalSidang()
    {
        return $this->belongsTo(JadwalSidangTugasAkhir::class);
    }
}
