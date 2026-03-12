<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhirTA extends Model
{
    protected $table = 'hasil_akhir_ta';
    protected $fillable = [
        'mahasiswa_id',
        'jadwal_sidang_tugas_akhir_id',
        'kaprodi_id',
        'nilai_pembimbing_utama',
        'nilai_pembimbing_pendamping',
        'nilai_penguji_utama',
        'nilai_penguji_pendamping',
        'total_akhir'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function jadwalSidang()
    {
        return $this->belongsTo(JadwalSidangTugasAkhir::class, 'jadwal_sidang_tugas_akhir_id');
    }

    public function kaprodi()
    {
        return $this->belongsTo(Dosen::class, 'kaprodi_id');
    }

    public function jadwalSidangTugasAkhir()
    {
        return $this->hasOne(JadwalSidangTugasAkhir::class, 'mahasiswa_id');
    }

    public function hasilSidang()
    {
        return $this->hasOne(HasilSidang::class, 'mahasiswa_id');
    }
}
