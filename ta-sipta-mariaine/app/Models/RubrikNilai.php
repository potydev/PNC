<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RubrikNilai extends Model
{
    protected $table = 'rubrik_nilai';
    protected $fillable = ['program_studi_id', 'jenis_dosen', 'kelompok', 'kategori', 'persentase'];
    public function penilaian()
    {
        return $this->hasMany(PenilaianTA::class, 'rubrik_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }
}
