<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * ShowcaseController
 * 
 * Displays portfolio of TRPL (Teknologi Rekayasa Perangkat Lunak) applications.
 * Provides centralized dashboard with links to available applications.
 * Requires authentication via SSO.
 * 
 * @category Controller
 * @package App\Http\Controllers
 */
class ShowcaseController extends Controller
{
    /**
     * Display Application Showcase Dashboard
     * 
     * Returns view with list of available TRPL applications:
     * - JKB Sistem Dosen Wali (port 8000)
     * - JKB Sistem Perkuliahan (port 8001)
     * - TA SIPTA Mariaine (port 8002)
     * 
     * Accessible only to authenticated users via middleware.
     * 
     * @return View Blade view with applications list and title
     */
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
