<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Penilaian Tugas Akhir</title>
    <style>
        body {
           font-family: "Times New Roman", Times, serif;
        }

        .container {
            padding-top: 0%;
            padding-bottom: 0%;
            padding-left: 40px;
            padding-right: 40px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
            vertical-align: top;
        }

        .kop-surat {
            margin-bottom: 10px;
        }

        .title-box {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        .info {
            text-align: justify;
            font-size: 16px;
            margin-top: 0;
        }

        .status-box {
            border: 0.5px solid #333;
            border-radius: 6px;
            text-align: center;
            padding: 4px;
            margin-top: 20px;
            font-weight: bold;
            font-size: 16px;
            background: #fff;
        }

        .footer {
            margin-top: 10px;
            text-align: right;
        }

        .instansi {
            text-align: left;
            padding-top: 0; /* biar mulai dari atas */
            line-height: 1.2;
        }

        .instansi p {
            margin: 2px 0;
            font-size: 12px;
        }

        .instansi .nama-instansi {
            font-size: 16px;
            font-weight: bold;
        }

         .table-ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        .table-ttd td {
            border: 1px solid #000;
            padding: 16px;
            vertical-align: top;
            font-family: "Times New Roman", Times, serif;
        }

        .col-jabatan {
            width: 40%;
        }

        .col-ttd {
            width: 60%;
            height: 20px;
        }

        .data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 16px;
            font-family: "Times New Roman", Times, serif;

        }

        .data td, .data th {
            border: 1px solid #000;
            padding: 6px;
        }

        .no-border td {
            border: none;
            /* padding: 3px 6px; */
        }

        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }

        .ttd{
            padding-top: 1px;
            padding-bottom: 1px;
            height: 56px;
        }

        .p-ttd{
            vertical-align: middle;
        }
    </style>

</head>
<body>

    <div class="container">
        <div class="kop-surat">
                <table>
                    <tr>
                        <td style="width: 15%; text-align: left;">
                            <img src="{{ public_path('img/pnc.png') }}" alt="Logo PNC" style="height: 90px;">
                        </td>
                        <td class="instansi" style="width: 70%; text-align: center; line-height: 1; letter-spacing: 1px;">
                            <p style="font-size: 18px; margin: 1 px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br> RISET, DAN TEKNOLOGI</p>
                            <p class="nama-instansi" style="font-size: 18px; font-weight: bold; margin: 1 px;">POLITEKNIK NEGERI CILACAP</p>
                            <p style="font-size: 14px; margin: 1 px;">Jalan Dr. Soetomo No. 1, Sidakaya – CILACAP 53212 Jawa Tengah</p>
                            <p style="font-size: 14px; margin: 1 px;">Telepon: (0282) 533329, Fax: (0282) 537992</p>
                            <p style="font-size: 14px; margin: 1 px;">www.pnc.ac.id, Email: sekretariat@pnc.ac.id</p>
                        </td>
                    </tr>
                </table>
                <hr style="border: 1px solid black; margin-top: 8px;">
            </div>

        <div class="title-box" style="letter-spacing: 0.5px;">
            PENILAIAN TUGAS AKHIR
        </div>

        <div class="info">

        <p>Hasil Evaluasi Sidang Akhir Untuk Mahasiswa:</p>

            <table class="no-border">
                <tr>
                    <td style="width: 40%;">Nama</td>
                    <td>: {{ $sidang->mahasiswa->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>: {{ $sidang->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: {{ $sidang->mahasiswa->programStudi->nama_prodi }}</td>
                </tr>

                <tr>
                    <td>Judul Tugas Akhir</td>
                    <td>: {{ $sidang->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $sidang->waktu_mulai ?? '-' }} - {{ $sidang->waktu_selesai ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Ruangan</td>
                    <td>: {{ $sidang->ruanganSidang->nama_ruangan }}</td>
                </tr>
                <br>
            </table>

            <table class="data">
                <thead>
                    <tr class="font-bold">
                        <th style="width: 60%;">Kategori</th>
                        <th style="width: 10%;">Bobot (%)</th>
                        <th style="width: 15%;">Nilai</th>
                        <th style="width: 15%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalJumlah = 0; @endphp
                    @foreach ($sidang->rubrik as $r)
                        @php
                            $jumlah = $r->nilai && $r->persentase ? $r->nilai * $r->persentase / 100 : 0;
                            $totalJumlah += $jumlah;
                        @endphp

                        @if ($r->show_kelompok)
                            <tr class="font-bold">
                                <td colspan="4">{{ $r->kelompok }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>
                                @if ($r->kelompok)
                                    &emsp;— {{ $r->kategori }}
                                @else
                                    {{ $r->kategori }}
                                @endif
                            </td>
                            <td class="text-center">{{ $r->persentase }}</td>
                            <td class="text-center">{{ $r->nilai }}</td>
                            <td class="text-center">{{ number_format($jumlah, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-bold">
                        <td colspan="3" class="text-center">TOTAL</td>
                        <td class="text-center">{{ number_format($totalJumlah, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="footer" style="margin-top: 15px;">
                <table style="width: 30%; margin-left: auto;">
                    <tr>
                        <td style="text-align: center; font-size: 16px;">
                            <p><strong>Cilacap, {{ \Carbon\Carbon::parse($sidang->tanggal)->translatedFormat('d F Y') }}</strong></p>
                            {{-- <p style="margin-bottom: 80px;"> --}}
                            <p style="margin-bottom: 4px;">
                                Dosen {{ ucwords(str_replace('_', ' ', $sidang->peran)) }}
                            </p>
                            @if ($dosen->ttd_dosen && file_exists(storage_path('app/public/' . $dosen->ttd_dosen)))
                                <img src="{{ public_path('storage/' . $dosen->ttd_dosen) }}" alt="TTD Dosen" class="ttd">
                            @else
                                <p class="text-sm italic text-gray-500">TTD belum tersedia</p>
                            @endif
                            <p><strong>{{ $dosen->nama_dosen ?? '-' }}</strong></p>
                            <p><strong>{{ $dosen->nip ?? '-' }}</strong></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
