<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSeminarProposal extends Model
{
    use HasFactory;
    protected $table = 'jadwal_seminar_proposal';

    protected $fillable = [
        'mahasiswa_id',
        'penguji_utama_id',
        'penguji_pendamping_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan_sidang_id',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
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
        return $this->belongsTo(RuanganSidang::class);
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'mahasiswa_id');
    }
}
