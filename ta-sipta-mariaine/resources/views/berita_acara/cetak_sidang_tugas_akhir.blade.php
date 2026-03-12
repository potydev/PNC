<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Sidang Tugas Akhir</title>
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
            padding-top: 0;
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
            vertical-align: middle;
            font-family: "Times New Roman", Times, serif;
        }

        .col-jabatan {
            width: 40%;
            vertical-align: middle;
        }

        .col-ttd {
            width: 60%;
            height: 20px;
            text-align: center;
        }
        .ttd{
            height: 48px;
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
            BERITA ACARA {{ strtoupper($judulSidang) }}
        </div>

        <div class="info">
            <p>Pada hari ini, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} bertempat di Kampus Politeknik Negeri Cilacap telah melaksanakan {{ strtolower($judulSidang) }} Mahasiswa: </p>
            <table>
                <tr>
                    <td style="width: 35%;"><strong>Nama</strong></td>
                    <td>: {{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <td><strong>NIM</strong></td>
                    <td>: {{ $jadwal->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td><strong>Program Studi</strong></td>
                    <td>: {{ $jadwal->mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Judul Proposal Tugas Akhir</strong></td>
                    <td>: {{ $jadwal->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
                </tr>
            </table>

            <p>Dalam pelaksanaan {{ strtolower($judulSidang) }} ini mahasiswa bersangkutan diuji oleh Tim Penguji yang terdiri dari:</p>
            <table>
                <tr>
                    <td style="width: 35%"><strong>1. Penguji Utama</strong></td>
                    <td>: {{ $jadwal->pengujiUtama->nama_dosen }}</td>
                </tr>
                <tr>
                    <td><strong>2. Penguji Pendamping</strong></td>
                    <td>: {{ $jadwal->pengujiPendamping->nama_dosen }}</td>
                </tr>
            </table>

            <p>Dan berdasarkan hasil evaluasi, maka mahasiswa tersebut dinyatakan:</p>
            <div class="status-box">
                @if ($status)
                    {{ strtoupper($status) }}
                @else
                    BELUM DINILAI
                @endif
            </div>

            <div class="footer">
                <p>Cilacap, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</p>
            </div>

            <table class="table-ttd">
                <tr>
                    <td class="col-jabatan">Mahasiswa</td>
                    <td class="col-ttd td-center">
                        @if ($jadwal->mahasiswa->ttd_mahasiswa)
                            <img src="{{ public_path('storage/' . $jadwal->mahasiswa->ttd_mahasiswa) }}" alt="TTD Mahasiswa" class="ttd">
                        @else
                            <span>Belum ada tanda tangan</span>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="col-jabatan">Pembimbing Utama</td>
                    <td class="col-ttd td-center">
                        @if ($jadwal->pembimbingUtama->ttd_dosen)
                            <img src="{{ public_path('storage/' . $jadwal->pembimbingUtama->ttd_dosen) }}" alt="TTD Pembimbing Utama" class="ttd">
                        @else
                            <span>Belum ada tanda tangan</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="col-jabatan">Pembimbing Pendamping</td>
                    <td class="col-ttd td-center">
                        @if ($jadwal->pembimbingPendamping->ttd_dosen)
                            <img src="{{ public_path('storage/' . $jadwal->pembimbingPendamping->ttd_dosen) }}" alt="TTD Pembimbing Pendamping" class="ttd">
                        @else
                            <span>Belum ada tanda tangan</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="col-jabatan">Ketua Penguji</td>
                    <td class="col-ttd td-center">
                        @if ($jadwal->pengujiUtama->ttd_dosen)
                            <img src="{{ public_path('storage/' . $jadwal->pengujiUtama->ttd_dosen) }}" alt="TTD Penguji Utama" class="ttd">
                        @else
                            <span>Belum ada tanda tangan</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="col-jabatan">Anggota Penguji</td>
                    <td class="col-ttd td-center">
                        @if ($jadwal->pengujiPendamping->ttd_dosen)
                            <img src="{{ public_path('storage/' . $jadwal->pengujiPendamping->ttd_dosen) }}" alt="TTD Penguji Pendamping" class="ttd">
                        @else
                            <span>Belum ada tanda tangan</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
