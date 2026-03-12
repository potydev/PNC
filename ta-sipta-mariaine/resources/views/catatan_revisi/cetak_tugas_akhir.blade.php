<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Revisi Seminar Proposal</title>
    <style>
        body {
           font-family: "Times New Roman", Times, serif;
        }

        .container {
            padding-left: 40px;
            padding-right: 40px;
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

        .instansi {
            text-align: left;
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

        .data-table td, .data-table th {
            border: none;
            padding: 6px 4px;
        }

        .kotak-revisi {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
            min-height: 150px;
            font-size: 15px;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .ttd {
            padding-top: 1px;
            padding-bottom: 1px;
            height: 56px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="kop-surat">
            <table>
                <tr>
                    <td style="width: 15%;">
                        <img src="{{ public_path('img/pnc.png') }}" alt="Logo PNC" style="height: 90px;">
                    </td>
                    <td class="instansi" style="width: 85%; text-align: center;">
                        <p style="font-size: 18px;">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</p>
                        <p style="font-size: 18px;">RISET, DAN TEKNOLOGI</p>
                        <p class="nama-instansi">POLITEKNIK NEGERI CILACAP</p>
                        <p>Jalan Dr. Soetomo No. 1, Sidakaya – CILACAP 53212 Jawa Tengah</p>
                        <p>Telepon: (0282) 533329, Fax: (0282) 537992</p>
                        <p>www.pnc.ac.id, Email: sekretariat@pnc.ac.id</p>
                    </td>
                </tr>
            </table>
            <hr style="border: 1px solid black; margin-top: 8px;">
        </div>

        <div class="title-box">
            FORM REVISI SIDANG TUGAS AKHIR
        </div>

        <div class="info">
            <p>Hasil Revisi Sidang Tugas Akhir untuk mahasiswa:</p>
            <table class="data-table">
                <tr>
                    <td style="width: 40%;">Nama</td>
                    <td>: {{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <td>NIM</td>
                    <td>: {{ $jadwal->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td>: {{ $jadwal->mahasiswa->programStudi->nama_prodi }}</td>
                </tr>
                <tr>
                    <td>Judul Proposal</td>
                    <td>: {{ $jadwal->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>: {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</td>
                </tr>
                <tr>
                    <td>Ruangan</td>
                    <td>: {{ $jadwal->ruanganSidang->nama_ruangan }}</td>
                </tr>
            </table>

            <p class="font-bold mt-4">Catatan Revisi:</p>
            <div class="kotak-revisi">
                {!! nl2br(e($catatan->catatan_revisi)) !!}
            </div>

            <div class="footer">
                <table style="width: 35%; margin-left: auto;">
                    <tr>
                        <td style="text-align: center; font-size: 16px;">
                            <p><strong>Cilacap, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</strong></p>
                            <p style="margin-bottom: 4px;">Dosen {{ ucwords(str_replace('_', ' ', $peran)) }}</p>
                            @if ($dosen->ttd_dosen)
                                <p><img src="{{ public_path('storage/' . $dosen->ttd_dosen) }}" alt="TTD Dosen" class="ttd"></p>
                            @else
                                <p><em>Tanda tangan belum tersedia</em></p>
                            @endif
                            <p><strong>{{ $dosen->nama_dosen }}</strong></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
