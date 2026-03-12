<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianSempro extends Model
{
    protected $table = 'penilaian_sempro';

    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'jadwal_seminar_proposal_id',
        'nilai',
        'catatan_revisi'
    ];

    // Relasi ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    // Relasi ke Jadwal Seminar Proposal
    public function jadwalSeminar()
    {
        return $this->belongsTo(JadwalSeminarProposal::class, 'jadwal_seminar_proposal_id');
    }
}
