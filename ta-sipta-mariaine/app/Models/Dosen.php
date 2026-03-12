<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nama_dosen',
        'nip',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jabatan',
        'program_studi_id',
        'ttd_dosen',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function pembimbingUtama()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_utama_id');
    }

    public function pembimbingPendamping()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_pendamping_id');
    }

    public function jadwalBimbingan()
    {
        return $this->hasMany(JadwalBimbingan::class);
    }
}
