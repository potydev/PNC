<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nama_mahasiswa',
        'nim',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'program_studi_id',
        'tahun_ajaran_id',
        'ttd_mahasiswa',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function proposal()
    {
        return $this->hasOne(Proposal::class);
    }

    public function pengajuanPembimbing()
    {
        return $this->hasOne(PengajuanPembimbing::class, 'mahasiswa_id');
    }

    public function logbooks()
    {
        return $this->hasMany(LogbookBimbingan::class);
    }

    public function jadwalSeminarProposal()
    {
        return $this->hasOne(JadwalSeminarProposal::class, 'mahasiswa_id');
    }

    public function jadwalSidangTugasAkhir()
    {
        return $this->hasOne(JadwalSidangTugasAkhir::class, 'mahasiswa_id');
    }

    public function pendaftaranSidang()
    {
        return $this->hasOne(PendaftaranSidang::class);
    }

    public function penilaianSempro()
    {
        return $this->hasMany(PenilaianSempro::class);
    }

    public function penilaianTA()
    {
        return $this->hasMany(PenilaianTA::class);
    }

    public function hasilAkhirSempro()
    {
        return $this->hasOne(HasilAkhirSempro::class);
    }

    public function hasilAkhirTA()
    {
        return $this->hasOne(HasilAkhirTA::class);
    }

    public function hasilSidang()
    {
        return $this->hasOne(HasilSidang::class);
    }
}
