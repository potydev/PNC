<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecturer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'number_phone',
        'address',
        'nidn',
        'nip',
        'signature',
        'user_id',
        'position_id',
    ];

    // Akun login yang terhubung ke data dosen.
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Relasi pivot dosen-mata kuliah.
    public function courseLecturers()
    {
        return $this->hasMany(CourseLecturer::class, 'lecturer_id');
    }

    // Daftar mata kuliah yang diampu dosen (many-to-many).
    public function courses()
    {
        return $this->belongsToMany(Courses::class, 'course_lecturers', 'lecturer_id', 'course_id')
                    ->using(CourseLecturer::class)
                    ->withPivot('id')
                    ->wherePivotNull('deleted_at');
    }

    // Alias relasi many-to-many mata kuliah (tetap dipertahankan untuk kompatibilitas kode lama).
    public function course(){
        return $this->belongsToMany(Courses::class, 'course_lecturers','lecturer_id', 'course_id')->wherePivotNull('deleted_at')
        ->withPivot('id');
    }
    
    // Jabatan struktural dosen.
    public function position()
    {
        return $this ->belongsTo(Position::class, 'position_id', 'id');
    }
}
