<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Proposal extends Model
{
    use HasFactory;
    protected $table = 'proposal';


    protected $fillable = ['mahasiswa_id', 'judul_proposal', 'file_proposal'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
