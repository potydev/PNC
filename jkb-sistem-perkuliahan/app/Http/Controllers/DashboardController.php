<?php

namespace App\Http\Controllers;

use App\Models\AttendanceList;
use App\Models\AttendanceListStudent;
use App\Models\Jadwal;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Contracts\Role;

class DashboardController extends Controller
{
    /**
    * Tampilkan dashboard utama berdasarkan role user yang login.
    *
    * - Menghitung ringkasan data user, dosen, mahasiswa
    * - Mengolah statistik absensi per status untuk chart
    * - Menyediakan data jadwal untuk mahasiswa
     */
    public function index(Request $request)
    {
        $auth = Auth::user();
        $user = User::count();
        $lecturer = Lecturer::count();
        $student = Student::count();

        // Filter opsional berdasarkan periode (format YYYY-MM)
        $periode = $request->input('periode');
    
        $availablePeriods = AttendanceListStudent::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period")
            ->groupBy('period')
            ->orderByDesc('period')
            ->pluck('period')
            ->toArray();

       
            
        // Query dasar data absensi; akan difilter jika periode dipilih
        $query = AttendanceListStudent::query();
        if ($periode) {
            $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$periode]);
        }

        $attendanceData = $query->get();

        
        // Inisialisasi semua status absensi untuk memastikan chart selalu konsisten
        $defaultCounts = [
            1 => 0, 
            2 => 0, 
            3 => 0, 
            4 => 0, 
            5 => 0, 
        ];

        foreach ($attendanceData as $record) {
            $status = $record->attendance_student;
            if (isset($defaultCounts[$status])) {
                $defaultCounts[$status]++;
            }
        }
        // Jadwal hanya relevan untuk role mahasiswa
        $jadwal = null;
        if($auth->hasRole('mahasiswa')){

            $jadwal = Jadwal::where('prodi_id', $auth->student->student_class->study_program_id)->first();
        }
        
        $labels = ['Hadir', 'Telat', 'Sakit', 'Izin', 'Bolos'];
        $data = array_values($defaultCounts);

        $prodis = StudyProgram::get();

        // Notifikasi awal login untuk dosen terkait password default
        $messagepass = null;
        $alertType = null;

        if ($auth->hasRole('dosen')){
            if (Hash::check($auth->lecturer->nidn, $auth->password)) {
                $messagepass = 'Ganti Password Terlebih Dahulu';
                $alertType = 'danger';
            } else {
                $messagepass = 'Berhasil Login!';
                $alertType = 'success';
            }
        }
        

        return view('masterdata.dashboard', [
            'auth' => $auth,
            'user' => $user,
            'lecturer' => $lecturer,
            'student' => $student,
            'attendanceCounts' => $defaultCounts,
            'labels' => $labels,
            'data' => $data,
            'availablePeriods' => $availablePeriods,
            'jadwal' => $jadwal,
            'prodis' => $prodis,
            'messagepass' => $messagepass,
            'alertType' => $alertType,
        ]);
    }

    /**
     * Tampilkan dashboard mahasiswa dan total akumulasi ketidakhadiran.
     */
    public function index_mahasiswa($studentId)
    {
        $auth = Auth::user();
        $attendances = AttendanceListStudent::with('detail')
        ->where('student_id', $studentId)
        ->where('attendance_student', 5)
        ->get();

         

        // Hitung total jam ketidakhadiran dengan status tertentu
        $totalJam = 0;

        // dd($attendances->pluck('attendanceListDetail'));

        foreach ($attendances as $attendance) {
            $detail = $attendance->detail;
//  dd($detail);
            if ($detail) {
                $durasi = (int)$detail->end_hour - (int)$detail->start_hour + 1;
                $totalJam += $durasi;
            }
        }

        $message = null;
        $alertType = null;

        // if ($totalJam < 30) {
        //                 $message = 'Anda memenuhi syarat untuk mengikuti UAS.';
        //     $alertType = 'success';
            
        // } else {
        //     $message = 'Mohon maaf, Anda tidak memenuhi syarat mengikuti UAS karena total ketidakhadiran Anda melebihi 30 jam.';
        //     $alertType = 'danger'; // bisa untuk styling alert

        // }
        $jadwal = null;
        if($auth->hasRole('mahasiswa')){

            $jadwal = Jadwal::where('prodi_id', $auth->student->student_class->study_program_id)->first();
        }

        // $password = DecryptException($auth->password);
        
        if ($auth->hasRole('mahasiswa')){
            if (Hash::check($auth->student->nim, $auth->password)) {
                $messagepass = 'Ganti Password Terlebih Dahulu';
                $alertType = 'danger';
            } else {
                $messagepass = 'Berhasil Login!';
                $alertType = 'success';
            }
        }
        
        
        

        return view('masterdata.dashboard', compact('totalJam', 'alertType','jadwal','messagepass'));
    }

   


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
