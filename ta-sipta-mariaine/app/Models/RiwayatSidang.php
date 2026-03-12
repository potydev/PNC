<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatSidang extends Model
{
    use HasFactory;

    protected $table = 'riwayat_sidang';

    protected $fillable = [
        'hasil_sidang_id',
        'jadwal_sidang_tugas_akhir_id',
        'status_sidang',
    ];

    public function hasilSidang()
    {
        return $this->belongsTo(HasilSidang::class);
    }

    public function jadwalSidangTugasAkhir()
    {
        return $this->belongsTo(JadwalSidangTugasAkhir::class);
    }

    public function hasilAkhirTA()
    {
        return $this->belongsTo(HasilAkhirTA::class, 'hasil_akhir_ta_id');
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalSidangTugasAkhir::class, 'jadwal_sidang_tugas_akhir_id');
    }
}
