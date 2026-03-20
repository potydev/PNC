<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceList extends Model
{
    use HasFactory;

    protected $table = 'attendance_lists';

    protected $fillable = [
        'code_al',
        'lecturer_id',
        'course_id',
        'student_class_id',
        'has_finished',
        'date_finished',
        'has_acc_kajur',
        'date_acc_kajur',
        'student_id',
        'lecturer_kajur_id',
        'periode_id',
    ];

    // Status penyelesaian dokumen: 0 = aktif, 1 = selesai, 2 = acc kajur.

    // Relasi ke dosen kajur yang melakukan approval.
    public function kajur()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_kajur_id', 'id');
    }

    // Relasi ke kelas mahasiswa.
    public function student_class(){
        return $this->belongsTo(StudentClass::class, 'student_class_id', 'id');
    }

    // Relasi ke dosen pengampu.
    public function lecturer(){
        return $this->belongsTo(Lecturer::class, 'lecturer_id', 'id');
    }

    // Relasi ke periode akademik.
    public function periode(){
        return $this->belongsTo(Periode::class, 'periode_id', 'id');
    }

    // Relasi ke mata kuliah.
    public function course(){
        return $this->belongsTo(Courses::class, 'course_id', 'id');
    }

    // Daftar detail kehadiran per pertemuan.
    public function attendanceListDetails(){
        return $this->hasMany(AttendanceListDetail::class, 'attendance_list_id','id');
    }

   // Relasi jurnal perkuliahan untuk dokumen ini.
   public function journal()
   {
       return $this->hasOne(Journal::class, 'attendance_list_id');
   }
}
