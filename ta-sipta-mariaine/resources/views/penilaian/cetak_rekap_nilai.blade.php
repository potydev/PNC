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
            text-align: center;
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

    <table class="data" style="margin-top: 20px; width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Mahasiswa</th>
            <th rowspan="2">NIM</th>
            <th rowspan="2">Program Studi</th>
            <th rowspan="2">Tahun Ajaran</th>
            <th colspan="2">Nilai</th>
        </tr>
        <tr>
            <th>Angka</th>
            <th>Huruf</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekap as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['nama'] }}</td>
                <td>{{ $item['nim'] }}</td>
                <td>{{ $item['prodi'] }}</td>
                <td>{{ $item['tahun_ajaran'] }}</td>
                <td>{{ $item['total_angka'] }}</td>
                <td>{{ $item['total_huruf'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>



    {{-- Tanda Tangan --}}
    <div class="footer">
        <table style="width: 30%; float: right;">
            <tr>
                <td style="text-align: center;">
                    {{-- <p><strong>Cilacap, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></p> --}}
                    <p><strong>Cilacap, {{ now()->translatedFormat('d F Y') }}</strong></p>
                    <p style="margin-bottom: 6px;">Koordinator Program Studi</p>
                    @if ($kaprodi && $kaprodi->ttd_dosen && file_exists(storage_path('app/public/' . $kaprodi->ttd_dosen)))
                        <img src="{{ public_path('storage/' . $kaprodi->ttd_dosen) }}" class="ttd" alt="TTD Kaprodi">
                    @else
                        <p class="italic" style="color: red;">TTD belum tersedia</p>
                    @endif
                    <p><strong>{{ $kaprodi->nama_dosen ?? '-' }}</strong></p>
                    <p><strong>{{ $kaprodi->nip ?? '-' }}</strong></p>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
