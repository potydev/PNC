<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PengajuanPembimbing extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_pembimbing';
    protected $fillable = [
        'mahasiswa_id',
        'pembimbing_utama_id',
        'pembimbing_pendamping_id',
        'validasi',
    ];

    public function pembimbingUtama()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_utama_id');
    }

    public function pembimbingPendamping()
    {
        return $this->belongsTo(Dosen::class, 'pembimbing_pendamping_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class);
    }
}
