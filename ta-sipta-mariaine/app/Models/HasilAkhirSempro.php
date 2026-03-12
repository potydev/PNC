<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilAkhirSempro extends Model
{
    protected $table = 'hasil_akhir_sempro';

    protected $fillable = [
        'mahasiswa_id',
        'jadwal_seminar_proposal_id',
        'nilai_penguji_utama',
        'nilai_penguji_pendamping',
        'total_akhir',
        'status_sidang',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(JadwalSeminarProposal::class, 'jadwal_seminar_prooposal_id');
    }
}
