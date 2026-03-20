<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ShowcaseController extends Controller
{
    public function index(): View
    {
        $apps = [
            [
                'name' => 'JKB Sistem Dosen Wali',
                'description' => 'Monitoring perwalian, validasi, dan manajemen dosen wali.',
                'url' => 'http://127.0.0.1:8000',
            ],
            [
                'name' => 'JKB Sistem Perkuliahan',
                'description' => 'Dokumen perkuliahan, jurnal, absensi, dan persetujuan.',
                'url' => 'http://127.0.0.1:8001',
            ],
            [
                'name' => 'TA SIPTA Mariaine',
                'description' => 'Aplikasi tugas akhir dan administrasi akademik terkait.',
                'url' => 'http://127.0.0.1:8002',
            ],
        ];

        return view('showcase.index', [
            'apps' => $apps,
            'title' => 'TRPL SHOWCASE',
        ]);
    }
}
