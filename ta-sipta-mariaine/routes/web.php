<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\HasilSidangController;
use App\Http\Controllers\PenilaianTAController;
use App\Http\Controllers\RubrikNilaiController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\RuanganSidangController;
use App\Http\Controllers\CatatanRevisiTAController;
use App\Http\Controllers\JadwalBimbinganController;
use App\Http\Controllers\PenilaianSemproController;
use App\Http\Controllers\HasilAkhirSemproController;
use App\Http\Controllers\LogbookBimbinganController;
use App\Http\Controllers\PendaftaranSidangController;
use App\Http\Controllers\PengajuanPembimbingController;
use App\Http\Controllers\JadwalSeminarProposalController;
use App\Http\Controllers\JadwalSidangTugasAkhirController;
use App\Models\JadwalSidangTugasAkhir;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// Route::get('/dashboard', function () {
//     return view('dashboard.index');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Route::prefix('dashboard')->group(function () {
//     Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
// });

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index'); // Menampilkan semua pengguna
    // Route::put('/{id}', [UserController::class, 'update'])->name('user.update');
    Route::get('/search', [UserController::class, 'search'])->name('user.search');
    Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.resetPassword'); // Mereset password pengguna
    Route::get('/dropdown-search', [UserController::class, 'dropdownSearch'])->name('user.dropdown-search');
});

Route::prefix('program_studi')->group(function () {
    Route::get('/', [ProgramStudiController::class, 'index'])->name('program_studi.index');
    Route::post('/', [ProgramStudiController::class, 'store'])->name('program_studi.store');
    Route::put('/{id}', [ProgramStudiController::class, 'update'])->name('program_studi.update');
    Route::get('/search', [ProgramStudiController::class, 'search'])->name('program_studi.search');
    Route::delete('/{id}', [ProgramStudiController::class, 'destroy'])->name('program_studi.destroy');
});

Route::prefix('ruangan_sidang')->group(function () {
    Route::get('/', [RuanganSidangController::class, 'index'])->name('ruangan_sidang.index');
    Route::post('/', [RuanganSidangController::class, 'store'])->name('ruangan_sidang.store');
    Route::put('/{id}', [RuanganSidangController::class, 'update'])->name('ruangan_sidang.update');
    Route::get('/search', [RuanganSidangController::class, 'search'])->name('ruangan_sidang.search');
    Route::delete('/{id}', [RuanganSidangController::class, 'destroy'])->name('ruangan_sidang.destroy');
});

Route::prefix('tahun_ajaran')->group(function () {
    Route::get('/', [TahunAjaranController::class, 'index'])->name('tahun_ajaran.index');
    Route::post('/', [TahunAjaranController::class, 'store'])->name('tahun_ajaran.store');
    Route::put('/{id}', [TahunAjaranController::class, 'update'])->name('tahun_ajaran.update');
    Route::get('/search', [TahunAjaranController::class, 'search'])->name('tahun_ajaran.search');
    Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('tahun_ajaran.destroy');
});

Route::prefix('mahasiswa')->group(function () {
    Route::get('/', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::put('/{id}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::put('/profile/edit/{id}', [MahasiswaController::class, 'update'])->name('mahasiswa.profile.update');
    Route::get('/search', [MahasiswaController::class, 'search'])->name('mahasiswa.search');
    Route::get('/dropdown-search', [MahasiswaController::class, 'dropdownSearch'])->name('mahasiswa.dropdown-search');
    Route::delete('/{id}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::post('/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::post('/unggah-ttd', [MahasiswaController::class, 'unggahTTD'])->name('mahasiswa.unggah_ttd');
});

Route::prefix('dosen')->group(function () {
    Route::get('/', [DosenController::class, 'index'])->name('dosen.index');
    Route::post('/', [DosenController::class, 'store'])->name('dosen.store');
    Route::put('/{id}', [DosenController::class, 'update'])->name('dosen.update');
    Route::put('/profile/edit/{id}', [DosenController::class, 'update'])->name('dosen.profile.update');
    Route::get('/search', [DosenController::class, 'search'])->name('dosen.search');
    Route::delete('/{id}', [DosenController::class, 'destroy'])->name('dosen.destroy');
    Route::get('/dosen/mahasiswa-bimbingan', [DosenController::class, 'mahasiswaBimbingan'])->name('dosen.mahasiswa-bimbingan');
    Route::post('/import', [DosenController::class, 'import'])->name('dosen.import');
    Route::post('/unggah-ttd', [DosenController::class, 'unggahTTD'])->name('dosen.unggah_ttd');
});

Route::prefix('proposal')->group(function () {
    Route::get('/', [ProposalController::class, 'index'])->name('proposal.index');
    Route::post('/', [ProposalController::class, 'store'])->name('proposal.store');
    Route::post('/update-file/{id}', [ProposalController::class, 'updateFileProposal'])->name('proposal.updateFile');
    Route::post('/{id}/revisi-mahasiswa', [ProposalController::class, 'updateRevisi'])->name('proposal.updateRevisi');
    Route::get('/{id}/proposal', [ProposalController::class, 'showFileProposal'])->name('proposal.showFileProposal');
    Route::get('/{id}/proposal-revisi', [ProposalController::class, 'showFileProposalRevisi'])->name('proposal.showFileProposalRevisi');
    Route::get('/{mahasiswaId}', [ProposalController::class, 'showKaprodi'])->name('proposal.show_kaprodi');
    Route::post('/{id}/catatan-revisi', [ProposalController::class, 'simpanCatatanRevisi'])->name('proposal.simpanCatatanRevisi');
});

Route::prefix('pengajuan_pembimbing')->group(function () {
    Route::get('/dosen', [PengajuanPembimbingController::class, 'index'])->name('pengajuan_pembimbing.index');
    Route::get('/list-pengajuan', [PengajuanPembimbingController::class, 'indexKaprodi'])->name('pengajuan_pembimbing.index_kaprodi');
    Route::post('/', [PengajuanPembimbingController::class, 'store'])->name('pengajuan_pembimbing.store');
    Route::put('/{id}/validasi', [PengajuanPembimbingController::class, 'validasi'])->name('pengajuan_pembimbing.validasi');
    Route::put('/{id}', [PengajuanPembimbingController::class, 'update'])->name('pengajuan_pembimbing.update');
    Route::get('/search', [PengajuanPembimbingController::class, 'search'])->name('pengajuan_pembimbing.search');
    Route::get('/list-pengajuan/dropdown-search', [PengajuanPembimbingController::class, 'dropdownSearch'])->name('pengajuan_pembimbing.index_kaprodi.dropdown-search');
    Route::get('/dosen/dropdown-search', [PengajuanPembimbingController::class, 'dropdownSearchDosen'])->name('pengajuan_pembimbing.index.dropdown-search_dosen');
    Route::delete('/{id}', [PengajuanPembimbingController::class, 'destroy'])->name('pengajuan_pembimbing.destroy');
    Route::get('/rekap-dosen', [PengajuanPembimbingController::class, 'rekapDosenPembimbing'])->name('pengajuan_pembimbing.rekap_dosen');
});

Route::prefix('jadwal_bimbingan')->group(function () {
    Route::get('/dosen', [JadwalBimbinganController::class, 'index'])->name('jadwal_bimbingan.index');
    Route::get('/list-bimbingan', [JadwalBimbinganController::class, 'indexKaprodi'])->name('jadwal_bimbingan.index_kaprodi');
    Route::post('/', [JadwalBimbinganController::class, 'store'])->name('jadwal_bimbingan.store');
    Route::post('/daftar/{id}', [JadwalBimbinganController::class, 'daftarBimbingan'])->name('jadwal_bimbingan.daftar');
    Route::post('/{id}/konfirmasi/{pendaftaranId}', [JadwalBimbinganController::class, 'konfirmasiBimbingan'])->name('jadwal_bimbingan.konfirmasi');
    Route::put('/{id}', [JadwalBimbinganController::class, 'update'])->name('jadwal_bimbingan.update');
    Route::get('/detail/{id}', [JadwalBimbinganController::class, 'detail'])->name('jadwal_bimbingan.detail');
    Route::get('/list-bimbingan/dropdown-search', [JadwalBimbinganController::class, 'dropdownSearch'])->name('jadwal_bimbingan.index_kaprodi.dropdown-search');
    Route::delete('/{id}', [JadwalBimbinganController::class, 'destroy'])->name('jadwal_bimbingan.destroy');
});

Route::prefix('logbook_bimbingan')->group(function () {
    Route::get('/mahasiswa', [LogbookBimbinganController::class, 'indexMahasiswa'])->name('logbook_bimbingan.index_mahasiswa');
    Route::get('/{id}/logbook', [LogbookBimbinganController::class, 'showFile'])->name('logbook_bimbingan.showFile');
    Route::patch('/mahasiswa/{id}/update-permasalahan', [LogbookBimbinganController::class, 'updatePermasalahan'])->name('logbook_bimbingan.update_permasalahan');
    Route::get('/mahasiswa/{dosenId}/{mahasiswaId}', [LogbookBimbinganController::class, 'showMahasiswa'])->name('logbook_bimbingan.show_mahasiswa');    // Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');
    Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'showKaprodi'])->name('logbook_bimbingan.show_kaprodi');    // Route::get('/{mahasiswaId}', [LogbookBimbinganController::class, 'show'])->name('logbook_bimbingan.show');
    Route::post('/', [LogbookBimbinganController::class, 'store'])->name('logbook_bimbingan.store');
    Route::put('/{id}', [LogbookBimbinganController::class, 'update'])->name('logbook_bimbingan.update');
    Route::post('/{id}/beri-rekomendasi', [LogbookBimbinganController::class, 'beriRekomendasi'])->name('logbook_bimbingan.rekomendasi');
});

Route::prefix('pendaftaran_sidang')->group(function () {
    Route::get('/mahasiswa', [PendaftaranSidangController::class, 'index'])->name('pendaftaran_sidang.index');
    Route::get('/kaprodi', [PendaftaranSidangController::class, 'indexKaprodi'])->name('pendaftaran_sidang.index_kaprodi');
    Route::post('/', [PendaftaranSidangController::class, 'store'])->name('pendaftaran_sidang.store');
    Route::get('/file/{id}/{fileField}', [PendaftaranSidangController::class, 'showFile'])->name('pendaftaran_sidang.showFile');
    Route::get('/dropdown-search', [PendaftaranSidangController::class, 'dropdownSearch'])->name('pendaftaran_sidang.dropdown_search');
});

Route::prefix('jadwal_sidang')->group(function () {
    Route::get('/tugas_akhir', [JadwalSidangTugasAkhirController::class, 'index'])->name('jadwal_sidang_tugas_akhir.index');
    Route::post('/tugas_akhir/import', [JadwalSidangTugasAkhirController::class, 'import'])->name('jadwal_sidang_tugas_akhir.import');
    Route::put('/tugas_akhir/{id}', [JadwalSidangTugasAkhirController::class, 'update'])->name('jadwal_sidang_tugas_akhir.update');
    Route::get('/tugas_akhir/dropdown-search', [JadwalSidangTugasAkhirController::class, 'dropdownSearch'])->name('jadwal_sidang_tugas_akhir.dropdown-search');
    Route::get('/rekap/dosen-penguji', [JadwalSidangTugasAkhirController::class, 'rekapDosenPenguji'])->name('rekap.dosen_penguji');


    Route::get('/seminar_proposal', [JadwalSeminarProposalController::class, 'index'])->name('jadwal_seminar_proposal.index');
    Route::post('/seminar_proposal/import', [JadwalSeminarProposalController::class, 'import'])->name('jadwal_seminar_proposal.import');
    Route::put('/seminar_proposal/{id}', [JadwalSeminarProposalController::class, 'update'])->name('jadwal_seminar_proposal.update');
    Route::get('/seminar_proposal/dropdown-search', [JadwalSeminarProposalController::class, 'dropdownSearch'])->name('jadwal_seminar_proposal.dropdown-search');
});

Route::prefix('penilaian_sempro')->group(function () {
    Route::get('/', [PenilaianSemproController::class, 'indexProposalDosen'])->name('penilaian_sempro.index');
    Route::post('/store', [PenilaianSemproController::class, 'store'])->name('penilaian_sempro.store'); // ← Tambahkan {mahasiswa}
    Route::get('/catatan/form', [PenilaianSemproController::class, 'formTambahCatatan'])->name('penilaian_sempro.catatan.form');
    Route::post('/catatan/store', [PenilaianSemproController::class, 'simpanCatatan'])->name('penilaian_sempro.catatan.store');
    Route::get('/penilaian_seminar/cetak', [PenilaianSemproController::class, 'cetakFormRevisiSempro'])->name('penilaian_sempro.cetak_revisi');
    Route::get('/gabung-revisi-sempro/{jadwal}', [PenilaianSemproController::class, 'gabungRevisiSempro'])->name('penilaian_sempro.catatan.gabung');
});

Route::prefix('hasil_akhir_sempro')->group(function () {
    Route::get('/index', [HasilAkhirSemproController::class, 'index'])->name('hasil_akhir_sempro.index');
    Route::get('/dropdown-search', [HasilAkhirSemproController::class, 'dropdownSearch'])->name('hasil_akhir_sempro.dropdown-search');
});

Route::prefix('rubrik_nilai')->group(function () {
    Route::get('/', [RubrikNilaiController::class, 'index'])->name('rubrik_nilai.index');
    Route::post('/', [RubrikNilaiController::class, 'store'])->name('rubrik_nilai.store');
    Route::put('/{id}', [RubrikNilaiController::class, 'update'])->name('rubrik_nilai.update');
    // Route::get('/search', [RubrikNilaiController::class, 'search'])->name('rubrik_nilai.search');
    Route::delete('/{id}', [RubrikNilaiController::class, 'destroy'])->name('rubrik_nilai.destroy');
    Route::get('/dropdown-search', [RubrikNilaiController::class, 'dropdownSearch'])->name('rubrik_nilai.dropdown-search');
});

Route::prefix('penilaian_ta')->group(function () {
    Route::get('/rekap-nilai', [PenilaianTAController::class, 'indexRekapNilai'])->name('penilaian_ta.rekap_nilai');
    Route::get('/rekap-nilai/cetak', [PenilaianTAController::class, 'cetakRekapNilai'])->name('penilaian_ta.cetak_rekap');
    Route::get('/rekap-yudisium/cetak', [PenilaianTAController::class, 'cetakMahasiswaYudisium'])->name('penilaian_ta.cetak_rekap_yudisium');
    Route::get('/', [PenilaianTAController::class, 'indexTugasAkhirDosen'])->name('penilaian_ta.index');
    Route::post('/store', [PenilaianTAController::class, 'store'])->name('penilaian_ta.store');
    Route::get('/sidang/{sidang_id}', [PenilaianTAController::class, 'form'])->name('penilaian_ta.form');
    Route::get('/{id}/cetak', [PenilaianTAController::class, 'cetakPDF'])->name('penilaian_ta.cetak');
    Route::get('/gabung/{jadwal}', [PenilaianTAController::class, 'gabungPenilaian'])->name('penilaian_ta.gabung');
    Route::get('/penilaian-ta/lihat-nilai/{jadwal}', [PenilaianTAController::class, 'lihatNilaiTA'])->name('penilaian_ta.lihat_nilai');
});

Route::prefix('catatan_revisi_ta')->group(function () {
    Route::post('/store', [CatatanRevisiTAController::class, 'store'])->name('penilaian_ta.catatan.store');
    Route::get('/form', [CatatanRevisiTAController::class, 'form'])->name('penilaian_ta.catatan.form');
    Route::get('/cetak', [CatatanRevisiTAController::class, 'cetakRevisiTugasAkhir'])->name('penilaian_ta.catatan.cetak');
    Route::get('/gabung/{jadwal}', [CatatanRevisiTAController::class, 'gabungRevisi'])->name('penilaian_ta.catatan.gabung');
});

Route::prefix('hasil_sidang')->group(function () {
    Route::get('/tugas-akhir/index', [HasilSidangController::class, 'index'])->name('hasil_sidang.tugas_akhir.index');
    Route::get('tugas-akhir/mahasiswa', [HasilSidangController::class, 'indexMahasiswa'])->name('hasil_sidang.tugas_akhir.index_mahasiswa');
    Route::get('/tugas-akhir/dropdown-search', [HasilSidangController::class, 'dropdownSearch'])->name('hasil_sidang.tugas_akhir.dropdown-search');
    Route::get('tugas-akhir/{id}', [HasilSidangController::class, 'show'])->name('hasil_sidang.tugas_akhir.show');
    Route::get('/berita-acara/{hasil_sidang_id}/{riwayat_sidang_id}/cetak', [HasilSidangController::class, 'cetakBeritaAcara'])->name('hasil_sidang.cetak_berita_acara');
    Route::get('/tugas-akhir/{jadwal}/revisi', [HasilSidangController::class, 'lihatRevisi'])->name('hasil_sidang.tugas_akhir.revisi');
    Route::post('/{id}/upload-revisi', [HasilSidangController::class, 'uploadRevisi'])->name('hasil_sidang.upload_revisi');
    Route::get('/file-revisi/{id}', [HasilSidangController::class, 'showFileRevisi'])->name('hasil_sidang.show_file_revisi');
    Route::patch('/{id}/cek-kelengkapan', [HasilSidangController::class, 'cekKelengkapan'])->name('hasil_sidang.cek_kelengkapan');
});

Route::prefix('berita_acara')->group(function () {
    Route::get('/seminar-proposal', [BeritaAcaraController::class, 'seminarProposal'])->name('berita_acara.seminar_proposal');
    Route::get('/sidang-tugas-akhir', [BeritaAcaraController::class, 'sidangTugasAkhir'])->name('berita_acara.sidang_tugas_akhir');
    Route::get('/kaprodi/seminar-proposal/{id}', [BeritaAcaraController::class, 'seminarProposalKaprodi'])->name('kaprodi.berita.seminar');
    Route::get('/kaprodi/sidang-ta/show', [BeritaAcaraController::class, 'showKaprodi'])->name('berita_acara.sidang_tugas_akhir.show');
    Route::get('/seminar-proposal/cetak', [BeritaAcaraController::class, 'cetakSeminarProposal'])->name('berita_acara.seminar-proposal.cetak');
    Route::get('/seminar-proposal/lihat', [BeritaAcaraController::class, 'lihatSeminarProposal'])->name('berita_acara.seminar-proposal.lihat');
    Route::get('/sidang-tugas_akhir/cetak', [BeritaAcaraController::class, 'cetakSidangTugasAkhir'])->name('berita_acara.sidang-tugas-akhir.cetak');
    Route::get('/sidang-tugas_akhir/lihat', [BeritaAcaraController::class, 'lihatSidangTugasAkhir'])->name('berita_acara.sidang-tugas-akhir.lihat');
});

Route::get('/download-template/import-mahasiswa', function () {
    $filePath = storage_path('app/public/template_import/template_import_mahasiswa.xlsx');
    return Response::download($filePath, 'template_import_mahasiswa.xlsx');
})->name('template.download.mahasiswa');

Route::get('/download-template/import-dosen', function () {
    $filePath = storage_path('app/public/template_import/template_import_dosen.xlsx');
    return Response::download($filePath, 'template_import_dosen.xlsx');
})->name('template.download.dosen');

Route::get('/download-template/import-jadwal-sempro', function () {
    $filePath = storage_path('app/public/template_import/template_import_jadwal_sempro.xlsx');
    return Response::download($filePath, 'template_import_jadwal_sempro.xlsx');
})->name('template.download.jadwalsempro');

Route::get('/download-template/import-jadwal-sidang-ta', function () {
    $filePath = storage_path('app/public/template_import/template_import_jadwal_sidang_ta.xlsx');
    return Response::download($filePath, 'template_import_jadwal_sidang_ta.xlsx');
})->name('template.download.jadwalsidangta');



// Route::get('file_bimbingan/{filename}', function ($filename) {
//     // Memastikan file ada di storage dan dapat diakses
//     $path = storage_path('app/public/' . $filename);

//     if (!file_exists($path)) {
//         abort(404);  // Jika file tidak ada, tampilkan halaman 404
//     }
//     return Response::file($path);
// })->name('file_bimbingan');
