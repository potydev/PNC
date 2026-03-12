<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PengajuanPembimbing;
use Illuminate\Support\Facades\Auth;

\Carbon\Carbon::setLocale('id');


class PengajuanPembimbingController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosenLogin = $user->dosen;
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $pengajuanQuery = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($user->role === 'Mahasiswa') {
            $pengajuanPembimbing = $pengajuanQuery
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->paginate(10);

            // ambil dosen pembimbing utama: hanya dari prodi yang sama
            $prodiId = $user->mahasiswa->program_studi_id;
            $dosenPembimbingUtama = Dosen::where('program_studi_id', $prodiId)->get();

            // pembimbing pendamping: semua dosen
            $dosenPembimbingPendamping = Dosen::all();

            $mahasiswa = $user->mahasiswa;
            $proposal = $mahasiswa ? $mahasiswa->proposal : null;

            return view('pengajuan_pembimbing.index', compact(
                'pengajuanPembimbing',
                'dosenPembimbingUtama',
                'dosenPembimbingPendamping',
                'user',
                'proposal',
                'tahunAjaranList'
            ));
        } elseif ($user->role === 'Dosen' && $dosenLogin) {
            $pengajuanPembimbing = $pengajuanQuery
                ->where(function ($query) use ($dosenLogin) {
                    $query->where('pembimbing_utama_id', $dosenLogin->id)
                        ->orWhere('pembimbing_pendamping_id', $dosenLogin->id);
                })
                ->where('validasi', 'Acc')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('pengajuan_pembimbing.index', compact(
                'pengajuanPembimbing',
                'user',
                'tahunAjaranList'
            ));
        } else {
            abort(403, 'Unauthorized access.');
        }
    }

    public function indexKaprodi()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('message', 'Please log in to continue.');
        }

        $user = Auth::user();
        $dosen = $user->dosen;
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();
        $pengajuanQuery = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa.proposal']);

        if ($dosen->jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing = $pengajuanQuery
                ->whereHas('mahasiswa', function ($query) use ($dosen) {
                    $query->where('program_studi_id', $dosen->program_studi_id);
                })
                ->orderBy('created_at', 'desc')->paginate(10);
        } elseif ($dosen->jabatan === 'Super Admin') {
            $pengajuanPembimbing = $pengajuanQuery->orderBy('created_at', 'desc')->paginate(10);
        } else {
            abort(403, 'Unauthorized access.');
        }

        $dosenPembimbingUtama = Dosen::where('program_studi_id', $dosen->program_studi_id)->get();
        $dosenPembimbingPendamping = Dosen::all();
        $dosen = Dosen::all();

        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'tahunAjaranList', 'dosen', 'dosenPembimbingUtama', 'dosenPembimbingPendamping', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id|different:pembimbing_utama_id',
        ]);

        $mahasiswaId = Auth::user()->mahasiswa->id;

        PengajuanPembimbing::create([
            'mahasiswa_id' => $mahasiswaId,
            'pembimbing_utama_id' => $request->pembimbing_utama_id,
            'pembimbing_pendamping_id' => $request->pembimbing_pendamping_id,
        ]);

        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil ditambahkan');
    }

    public function validasi($id)
    {
        $pengajuan = PengajuanPembimbing::findOrFail($id);
        $pengajuan->validasi = 'Acc';
        $pengajuan->save();

        return redirect()->back()->with('success', 'Status validasi berhasil diperbarui menjadi Acc.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pembimbing_utama_id' => 'required|exists:dosen,id',
            'pembimbing_pendamping_id' => 'required|exists:dosen,id|different:pembimbing_utama_id',
        ]);

        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->update($request->all());

        $pengajuanPembimbing->validasi = 'Acc';
        $pengajuanPembimbing->save();
        return redirect()->route('pengajuan_pembimbing.index_kaprodi')->with('success', 'Pengajuan Pembimbing berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $jabatan = $user->dosen->jabatan;
        $programStudiId = $user->dosen->program_studi_id;

        $pengajuanPembimbing = PengajuanPembimbing::with(['mahasiswa', 'tahunAjaran', 'pembimbingUtama', 'pembimbingPendamping'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('mahasiswa', function ($q) use ($search) {
                    $q->where('nama_mahasiswa', 'like', "%$search%");
                })->orWhereHas('tahunAjaran', function ($q) use ($search) {
                    $q->where('tahun_ajaran', 'like', "%$search%");
                });
            });

        // Jika Kaprodi, filter berdasarkan program studi
        if ($jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($programStudiId) {
                $query->where('program_studi_id', $programStudiId);
            });
        }

        $pengajuanPembimbing = $pengajuanPembimbing->orderBy('created_at', 'desc')->paginate(10);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing'));
    }

    public function dropdownSearch(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen->id;
        $jabatan = $dosen->jabatan;
        $programStudiId = $dosen->program_studi_id;

        $dosen = Dosen::all(); // untuk dropdown
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        if ($jabatan === 'Koordinator Program Studi') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi))
                ->whereHas('mahasiswa', function ($query) use ($programStudiId, $tahunAjaranId) {
                    $query->where('program_studi_id', $programStudiId);
                    if ($tahunAjaranId) {
                        $query->where('tahun_ajaran_id', $tahunAjaranId);
                    }
                });
        } elseif ($jabatan === 'Super Admin') {
            $pengajuanPembimbing
                ->when($pembimbingUtamaId, fn($query) => $query->where('pembimbing_utama_id', $pembimbingUtamaId))
                ->when($pembimbingPendampingId, fn($query) => $query->where('pembimbing_pendamping_id', $pembimbingPendampingId))
                ->when($validasi, fn($query) => $query->where('validasi', $validasi))
                ->when($tahunAjaranId, function ($query) use ($tahunAjaranId) {
                    $query->whereHas('mahasiswa', function ($q) use ($tahunAjaranId) {
                        $q->where('tahun_ajaran_id', $tahunAjaranId);
                    });
                });
        } else {
            if ($searchType === 'Utama') {
                $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
            } elseif ($searchType === 'Pendamping') {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
            } else {
                $pengajuanPembimbing = PengajuanPembimbing::where(function ($query) use ($dosenId) {
                    $query->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId);
                })
                    ->where('validasi', 'Acc')
                    ->with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);
            }

            if ($tahunAjaranId) {
                $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                });
            }
        }

        $pengajuanPembimbing = $pengajuanPembimbing->orderBy('created_at', 'desc')->paginate(10);

        return view('pengajuan_pembimbing.index_kaprodi', compact('pengajuanPembimbing', 'dosen', 'user', 'tahunAjaranList'));
    }

    public function dropdownSearchDosen(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->dosen) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $dosenLogin = $user->dosen;
        $dosenId = $dosenLogin->id;
        $jabatan = $dosenLogin->jabatan;
        $programStudiId = $dosenLogin->program_studi_id;

        $dosen = Dosen::all();
        $tahunAjaranList = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        // Ambil nilai dari form
        $pembimbingUtamaId = $request->input('pembimbing_utama_id');
        $pembimbingPendampingId = $request->input('pembimbing_pendamping_id');
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        $validasi = $request->input('validasi');
        $searchType = $request->input('search_type');

        // Query dasar
        $pengajuanPembimbing = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa']);

        // Filter berdasarkan role
        if ($user->role === 'Dosen') {
            if ($searchType === 'Utama') {
                $pengajuanPembimbing->where('pembimbing_utama_id', $dosenId);
            } elseif ($searchType === 'Pendamping') {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $dosenId);
            } else {
                $pengajuanPembimbing->where(function ($query) use ($dosenId) {
                    $query->where('pembimbing_utama_id', $dosenId)
                        ->orWhere('pembimbing_pendamping_id', $dosenId);
                })->where('validasi', 'Acc');
            }
            if ($tahunAjaranId) {
                $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                });
            }
        } elseif ($user->role === 'Dosen' &&  ($user->dosen->jabatan === 'Super Admin' || $user->dosen->jabatan === 'Koordinator Program Studi')) {
            // Filter berdasarkan dosen jika dipilih
            if ($pembimbingUtamaId) {
                $pengajuanPembimbing->where('pembimbing_utama_id', $pembimbingUtamaId);
            }
            if ($pembimbingPendampingId) {
                $pengajuanPembimbing->where('pembimbing_pendamping_id', $pembimbingPendampingId);
            }

            // Filter tahun ajaran (via relasi mahasiswa)
            if ($tahunAjaranId) {
                $pengajuanPembimbing->whereHas('mahasiswa', function ($query) use ($tahunAjaranId) {
                    $query->where('tahun_ajaran_id', $tahunAjaranId);
                });
            }
        }

        $pengajuanPembimbing = $pengajuanPembimbing->orderBy('created_at', 'desc')->paginate(10);

        return view('pengajuan_pembimbing.index', compact('pengajuanPembimbing', 'dosen', 'tahunAjaranList', 'user'));
    }

    public function destroy(string $id)
    {
        $pengajuanPembimbing = PengajuanPembimbing::findOrFail($id);
        $pengajuanPembimbing->delete();
        return redirect()->route('pengajuan_pembimbing.index')->with('success', 'Pengajuan Pembimbing berhasil dihapus');
    }

    public function rekapDosenPembimbing(Request $request)
    {
        $user = Auth::user();

        if (
            !$user ||
            !in_array(optional($user->dosen)->jabatan, ['Koordinator Program Studi', 'Super Admin'])
        ) {
            return redirect('/login')->with('message', 'Unauthorized');
        }

        $programStudiId = $user->dosen->program_studi_id;
        $tahunAjaranId = $request->input('tahun_ajaran');

        $data = PengajuanPembimbing::with(['pembimbingUtama', 'pembimbingPendamping', 'mahasiswa'])
            ->whereHas('mahasiswa', function ($q) use ($programStudiId, $tahunAjaranId) {
                $q->where('program_studi_id', $programStudiId);
                if ($tahunAjaranId) {
                    $q->where('tahun_ajaran_id', $tahunAjaranId);
                }
            })
            ->where('validasi', 'Acc')
            ->get();

        $grouped = [];

        foreach ($data as $item) {
            $mhs = $item->mahasiswa;

            // Pembimbing Utama
            if ($item->pembimbingUtama) {
                $nama = $item->pembimbingUtama->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Pembimbing Utama',
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                    'tahun_ajaran' => $mhs->tahunAjaran->tahun_ajaran ?? '',
                ];
            }

            // Pembimbing Pendamping
            if ($item->pembimbingPendamping) {
                $nama = $item->pembimbingPendamping->nama_dosen;

                if (!isset($grouped[$nama])) {
                    $grouped[$nama] = [
                        'nama_dosen' => $nama,
                        'detail' => [],
                    ];
                }

                $grouped[$nama]['detail'][] = (object)[
                    'peran' => 'Pembimbing Pendamping',
                    'nim' => $mhs->nim,
                    'nama_mahasiswa' => $mhs->nama_mahasiswa,
                    'tahun_ajaran' => $mhs->tahunAjaran->tahun_ajaran ?? '',
                ];
            }
        }

        $rekap = collect($grouped)
            ->flatMap(function ($item) {
                return collect($item['detail'])->map(function ($detail) use ($item) {
                    return [
                        'nama_dosen' => $item['nama_dosen'],
                        'peran' => $detail->peran,
                        'nim' => $detail->nim,
                        'nama_mahasiswa' => $detail->nama_mahasiswa,
                        'tahun_ajaran' => $detail->tahun_ajaran,
                    ];
                });
            })
            ->groupBy('tahun_ajaran')
            ->map(function ($dataPerTahun) {
                return collect($dataPerTahun)
                    ->groupBy('nama_dosen')
                    ->map(function ($items, $dosen) {
                        return [
                            'nama_dosen' => $dosen,
                            'detail' => collect($items)
                                ->sortBy([
                                    fn($a, $b) => strcmp($b['peran'], $a['peran']),
                                    fn($a, $b) => strcmp($a['nama_mahasiswa'], $b['nama_mahasiswa']),
                                ])
                                ->map(fn($x) => (object) $x)
                                ->values()
                                ->all()
                        ];
                    })
                    ->sortKeys()
                    ->values()
                    ->all();
            })
            ->sortKeysDesc();

        $pdf = Pdf::loadView('pengajuan_pembimbing.rekap_dosen_pembimbing', compact('rekap'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap_dosen_pembimbing.pdf');
    }
}
