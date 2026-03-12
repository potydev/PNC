<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProgramStudi extends Model
{
    use HasFactory;
    protected $table = 'program_studi';
    protected $fillable = ['kode_prodi', 'nama_prodi'];
}
