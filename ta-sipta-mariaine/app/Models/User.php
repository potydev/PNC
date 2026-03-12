<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }


    // public function dosen()
    // {
    //     return $this->belongsTo(Dosen::class, 'dosen_id');
    // }

    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }

    // public function isPengujiUtama()
    // {
    //     // Logika untuk memeriksa apakah user adalah penguji utama
    //     return $this->role === 'penguji_utama'; // Sesuaikan dengan logika Anda
    // }

    // public function isPengujiPendamping()
    // {
    //     // Logika untuk memeriksa apakah user adalah penguji pendamping
    //     return $this->role === 'penguji_pendamping'; // Sesuaikan dengan logika Anda
    // }

    // public function isPembimbingUtama()
    // {
    //     // Logika untuk memeriksa apakah user adalah pembimbing utama
    //     return $this->role === 'pembimbing_utama'; // Sesuaikan dengan logika Anda
    // }

    // public function isPembimbingPendamping()
    // {
    //     // Logika untuk memeriksa apakah user adalah pembimbing pendamping
    //     return $this->role === 'pembimbing_pendamping'; // Sesuaikan dengan logika Anda
    // }
}
