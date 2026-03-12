<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalBimbingan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_bimbingan';

    protected $fillable = [
        'dosen_id',
        'tanggal',
        'waktu',
        'kuota',
        'durasi',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function pendaftaranBimbingan()
    {
        return $this->hasMany(PendaftaranBimbingan::class, 'jadwal_bimbingan_id');
    }

    public function logbooks()
    {
        return $this->hasMany(LogbookBimbingan::class);
    }
}
