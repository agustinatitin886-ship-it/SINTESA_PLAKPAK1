<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Juara per Kelas - SDN Plakpak 1 Pegantenan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background: #ffffff;
            font-size: 12pt;
            line-height: 1.35;
            padding: 20px;
        }

        .paper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px 40px;
        }

        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .kop-surat h4 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .kop-surat h3 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .kop-surat p {
            margin: 4px 0 0 0;
            font-size: 10pt;
            font-style: italic;
        }

        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 24px 0;
            font-size: 11pt;
        }

        .table-laporan th, .table-laporan td {
            border: 1px solid #000000;
            padding: 6px 8px;
        }

        .table-laporan th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .class-heading {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 20px;
            margin-bottom: 6px;
            border-bottom: 1.5px solid #000000;
            padding-bottom: 4px;
        }

        .ttd-section {
            margin-top: 35px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 300px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .paper {
                padding: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
        <i class="bi bi-printer-fill me-1"></i> Cetak Rekap per Kelas Sekarang
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary btn-lg ms-2">Tutup</button>
</div>

<div class="paper">
    <!-- Kop Surat Resmi -->
    <div class="kop-surat">
        <h4>PEMERINTAH KABUPATEN PAMEKASAN</h4>
        <h4>DINAS PENDIDIKAN DAN KEBUDAYAAN</h4>
        <h3>SD NEGERI PLAKPAK 1 PEGANTENAN</h3>
        <p>Alamat: Jl. Raya Plakpak, Kecamatan Pegantenan, Kabupaten Pamekasan, Jawa Timur 69361<br>
        Email: sdnplakpak1@gmail.com | NPSN: 20529817</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1 text-uppercase" style="text-decoration: underline;">
            REKAPITULASI PENETAPAN SISWA BERPRESTASI PER TINGKAT KELAS
        </h5>
        <div style="font-size: 11pt;">
            Tahun Ajaran: <strong>{{ $selectedPeriode }}</strong>
            @if($selectedKelas !== 'all')
                | Khusus: <strong>{{ $selectedKelas }}</strong>
            @endif
        </div>
        <div style="font-size: 10pt; color: #444;" class="mt-1">
            Sistem Informasi Penilaian Siswa Berprestasi Metode BWM & WASPAS (SINTESA)
        </div>
    </div>

    @if($juaraUmum && $selectedKelas === 'all')
        <!-- Box Juara Umum -->
        <div style="border: 2px solid #000; padding: 10px 14px; margin-bottom: 20px; background-color: #fafafa;">
            <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase;">
                Penetapan Juara Umum Sekolah:
            </div>
            <div style="font-size: 12pt; font-weight: bold; margin-top: 4px;">
                {{ $juaraUmum->siswa->nama_lengkap ?? '-' }} &bull; {{ $juaraUmum->siswa->kelas ?? '-' }}
            </div>
            <div style="font-size: 10pt;">
                NISN: {{ $juaraUmum->siswa->nisn ?? '-' }} | Skor WASPAS Akhir ($Q_i$): <strong>{{ number_format($juaraUmum->nilai_qi, 4) }}</strong> | Peringkat Sekolah: #1
            </div>
        </div>
    @endif

    <!-- Daftar Tiap Kelas -->
    @foreach($rekapPerKelas as $kls => $siswaList)
        <div class="class-heading">
            Daftar Peringkat {{ $kls }} &bull; Tahun Ajaran {{ $selectedPeriode }}
        </div>
        <table class="table-laporan">
            <thead>
                <tr>
                    <th style="width: 70px;">Juara / Rank</th>
                    <th style="width: 120px;">NISN</th>
                    <th>Nama Lengkap Peserta Didik</th>
                    <th style="width: 50px;">L/P</th>
                    <th style="width: 80px;">Skor Qi</th>
                    <th style="width: 80px;">Peringkat Umum</th>
                    <th style="width: 160px;">Status Kelayakan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $item)
                    <tr>
                        <td class="text-center fw-bold">
                            @if($item->peringkat_kelas == 1)
                                Juara 1
                            @elseif($item->peringkat_kelas == 2)
                                Juara 2
                            @elseif($item->peringkat_kelas == 3)
                                Juara 3
                            @else
                                {{ $item->peringkat_kelas }}
                            @endif
                        </td>
                        <td class="text-center">{{ $item->siswa->nisn ?? '-' }}</td>
                        <td class="fw-bold">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                        <td class="text-center">{{ $item->siswa->jenis_kelamin ?? 'L' }}</td>
                        <td class="text-center fw-bold">{{ number_format($item->nilai_qi, 4) }}</td>
                        <td class="text-center">#{{ $item->peringkat }}</td>
                        <td class="text-center">
                            @if($item->peringkat_kelas == 1)
                                <strong>Sangat Layak (Juara 1)</strong>
                            @elseif($item->peringkat_kelas == 2)
                                Layak (Juara 2)
                            @elseif($item->peringkat_kelas == 3)
                                Layak (Juara 3)
                            @else
                                Nominasi
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <!-- Tanda Tangan -->
    <div class="ttd-section">
        <div class="ttd-box">
            <div>Pegantenan, {{ $approval && $approval->tanggal_persetujuan ? $approval->tanggal_persetujuan->translatedFormat('d F Y') : date('d F Y') }}</div>
            <div class="fw-bold mt-1">Kepala SD Negeri Plakpak 1,</div>
            <div style="height: 65px;"></div>
            <div class="fw-bold text-decoration-underline">
                {{ $approval?->disetujuiOleh?->nama_pengguna ?? 'H. Achmad Zaini, S.Pd., M.Pd.' }}
            </div>
            <div>NIP. 19680512 199203 1 008</div>
        </div>
    </div>
</div>

</body>
</html>

