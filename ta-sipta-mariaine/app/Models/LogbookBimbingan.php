<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogbookBimbingan extends Model
{
    use HasFactory;

    protected $table = 'logbook_bimbingan';

    protected $fillable = [
        'mahasiswa_id',
        'pendaftaran_bimbingan_id',
        'permasalahan',
        'file_bimbingan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function jadwalBimbingan()
    {
        return $this->belongsTo(JadwalBimbingan::class, 'jadwal_bimbingan_id');
    }

    public function pendaftaranBimbingan()
    {
        return $this->belongsTo(PendaftaranBimbingan::class, 'pendaftaran_bimbingan_id');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class);
    }
}
