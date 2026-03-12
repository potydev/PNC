<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Penilaian Akhir Mahasiswa</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
        }

        .container {
            padding-left: 40px;
            padding-right: 40px;
            background: #fff;
        }

        .instansi p {
            margin: 2px 0;
            font-size: 12px;
        }

        .instansi .nama-instansi {
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border td {
            border: none;
            padding: 4px 0;
        }

        .data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 16px;
        }

        .data td, .data th {
            border: 1px solid #000;
            padding: 6px;
        }

        .font-bold { font-weight: bold; }
        .text-center { text-align: center; }

        .ttd {
            height: 56px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
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

    </style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <table>
        <tr>
            <td style="width: 15%;">
                <img src="{{ public_path('img/pnc.png') }}" alt="Logo PNC" style="height: 90px;">
            </td>
            <td class="instansi" style="text-align: center;">
                <p style="font-size: 18px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, <br> RISET, DAN TEKNOLOGI</p>
                <p class="nama-instansi">POLITEKNIK NEGERI CILACAP</p>
                <p>Jalan Dr. Soetomo No. 1, Sidakaya – CILACAP 53212 Jawa Tengah</p>
                <p>Telepon: (0282) 533329, Fax: (0282) 537992</p>
                <p>www.pnc.ac.id, Email: sekretariat@pnc.ac.id</p>
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid black; margin-top: 8px;">

    {{-- Judul --}}
    <h3 class="text-center" style="margin-top: 20px;">REKAP NILAI TUGAS AKHIR</h3>

    <p>Pada hari ini, {{ \Carbon\Carbon::parse($hasilAkhir->jadwalSidangTugasAkhir->tanggal)->translatedFormat('d F Y') }} bertempat di Kampus Politeknik Negeri Cilacap telah melaksanakan Sidang Tugas Akhir Mahasiswa: </p>

    {{-- Info Mahasiswa --}}
    <table class="no-border" style="margin-top: 10px;">
        <tr>
            <td style="width: 35%;">Nama Mahasiswa</td>
            <td>: {{ $hasilAkhir->mahasiswa->nama_mahasiswa ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>: {{ $hasilAkhir->mahasiswa->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>: {{ $hasilAkhir->mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
        </tr>
        <tr>
            <td>Judul Tugas Akhir</td>
            <td>: {{ $hasilAkhir->mahasiswa->proposal->revisi_judul_proposal ?? $hasilAkhir->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Sidang</td>
            <td>: {{ \Carbon\Carbon::parse($hasilAkhir->jadwalSidangTugasAkhir->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p>Dalam pelaksanaan Sidang Tugas Akhir ini mahasiswa bersangkutan mendapatkan nilai: </p>

    {{-- Nilai --}}
    <table class="data" style="margin-top: 20px;">
        <thead>
            <tr>
                <th></th>
                <th class="text-center">Bobot (%)</th>
                <th class="text-center">Nilai</th>
                <th class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nilai as $label => $item)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-center">{{ $item['bobot'] }}</td>
                    <td class="text-center">{{ $item['nilai'] ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item['jumlah'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="font-bold">
                <td colspan="3" class="text-center">TOTAL</td>
                <td class="text-center">{{ number_format($totalJumlah, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Status Kelulusan --}}
     <p>Dan berdasarkan hasil evaluasi dewan penguji, maka mahasiswa tersebut dinyatakan:</p>
    <div class="status-box">
        @if ($statusKelulusan)
            {{ strtoupper($statusKelulusan) }}
        @else
            BELUM DINILAI
        @endif
    </div>

    {{-- Tanda Tangan --}}
    <div class="footer">
        <table style="width: 30%; float: right;">
            <tr>
                <td style="text-align: center;">
                    {{-- <p><strong>Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></p> --}}
                    <p><strong>Cilacap, {{ \Carbon\Carbon::parse($hasilAkhir->jadwalSidangTugasAkhir->tanggal)->translatedFormat('d F Y') }}</strong></p>
                    <p style="margin-bottom: 6px;">Koordinator Program Studi</p>
                    @if ($hasilAkhir->kaprodi && $hasilAkhir->kaprodi->ttd_dosen && file_exists(storage_path('app/public/' . $hasilAkhir->kaprodi->ttd_dosen)))
                        <img src="{{ public_path('storage/' . $hasilAkhir->kaprodi->ttd_dosen) }}" class="ttd" alt="TTD Kaprodi">
                    @else
                        <p class="italic" style="color: red;">TTD belum tersedia</p>
                    @endif
                    <p><strong>{{ $hasilAkhir->kaprodi->nama_dosen ?? '-' }}</strong></p>
                    <p><strong>{{ $hasilAkhir->kaprodi->nip ?? '-' }}</strong></p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
