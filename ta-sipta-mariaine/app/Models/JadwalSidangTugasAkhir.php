<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSidangTugasAkhir extends Model
{
    use HasFactory;

    protected $table = 'jadwal_sidang_tugas_akhir';

    protected $fillable = [
        'mahasiswa_id',
        'jenis_sidang',
        'pembimbing_utama_id',
        'pembimbing_pendamping_id',
        'penguji_utama_id',
        'penguji_pendamping_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan_sidang_id',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function pembimbingUtama()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_utama_id');
    }

    public function pembimbingPendamping()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_pendamping_id');
    }

    public function pengujiUtama()
    {
        return $this->belongsTo(Dosen::class, 'penguji_utama_id');
    }

    public function pengujiPendamping()
    {
        return $this->belongsTo(Dosen::class, 'penguji_pendamping_id');
    }

    public function ruanganSidang()
    {
        return $this->belongsTo(RuanganSidang::class, 'ruangan_sidang_id');
    }

    public function rubrik()
    {
        return RubrikNilai::where('peran', $this->peran)->get();
    }

    public function hasilSidang()
    {
        return $this->hasOne(HasilSidang::class, 'jadwal_sidang_tugas_akhir_id');
    }



    public function riwayatSidang()
    {
        return $this->hasOne(RiwayatSidang::class, 'jadwal_sidang_tugas_akhir_id');
    }
}
