<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Siswa Berprestasi - SDN Plakpak 1 Pegantenan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background: #ffffff;
            font-size: 13pt;
            line-height: 1.4;
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
            margin: 20px 0;
            font-size: 11pt;
        }

        .table-laporan th, .table-laporan td {
            border: 1px solid #000000;
            padding: 8px 10px;
        }

        .table-laporan th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 300px;
            text-align: center;
        }

        .stempel-badge {
            display: inline-block;
            border: 2px solid #166534;
            color: #166534;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 9pt;
            font-family: sans-serif;
            margin: 10px 0;
            font-weight: bold;
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
        <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen Sekarang
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

    <!-- Title of Report -->
    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1 text-uppercase" style="text-decoration: underline;">
            LAPORAN REKAPITULASI PENETAPAN SISWA BERPRESTASI
        </h5>
        <div style="font-size: 11pt;">
            Tahun Ajaran: <strong>{{ $selectedPeriode }}</strong>
            @if($selectedKelas !== 'all')
                | Kelas: <strong>{{ $selectedKelas }}</strong>
            @endif
        </div>
        <div style="font-size: 10pt; color: #444;" class="mt-1">
            Metode: Best-Worst Method (BWM) & Weighted Aggregated Sum Product Assessment (WASPAS)
        </div>
    </div>

    <!-- Criteria Table Summary -->
    <div class="mb-3" style="font-size: 10pt;">
        <strong>Kriteria Penilaian yang Digunakan:</strong>
        <div class="d-flex flex-wrap gap-3 mt-1">
            @foreach($kriteria as $k)
                <span><strong>{{ $k->kode_kriteria }}</strong>: {{ $k->nama_kriteria }} (Bobot: {{ round($k->bobot_bwm * 100, 2) }}%)</span>
            @endforeach
        </div>
    </div>

    <!-- Main Results Table -->
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 50px;">Peringkat</th>
                <th style="width: 120px;">NISN</th>
                <th>Nama Lengkap Siswa</th>
                <th style="width: 80px;">Kelas</th>
                <th style="width: 80px;">WSM (Q1)</th>
                <th style="width: 80px;">WPM (Q2)</th>
                <th style="width: 90px;">Skor Qi</th>
                <th style="width: 130px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $item)
                <tr>
                    <td class="text-center fw-bold">{{ $item->peringkat }}</td>
                    <td class="text-center">{{ $item->siswa->nisn ?? '-' }}</td>
                    <td class="fw-bold">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                    <td class="text-center">{{ $item->siswa->kelas ?? '-' }}</td>
                    <td class="text-center">{{ number_format($item->nilai_wsm, 4) }}</td>
                    <td class="text-center">{{ number_format($item->nilai_wpm, 4) }}</td>
                    <td class="text-center fw-bold">{{ number_format($item->nilai_qi, 4) }}</td>
                    <td class="text-center">
                        @if($item->peringkat == 1)
                            <strong>Juara I Utama</strong>
                        @elseif($item->peringkat == 2)
                            <strong>Juara II</strong>
                        @elseif($item->peringkat == 3)
                            <strong>Juara III</strong>
                        @else
                            Nominasi
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-3">Tidak ada data siswa untuk dicetak.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="ttd-section">
        <div class="ttd-box">
            <div>Pamekasan, {{ date('d F Y') }}</div>
            <div class="fw-bold mb-1">Kepala SD Negeri Plakpak 1</div>
            
            @if($approval && $approval->status === 'disetujui')
                <div class="stempel-badge">
                    <i class="bi bi-patch-check-fill me-1"></i> TERVALIDASI DIGITAL<br>
                    <small>SK No: {{ $approval->nomor_sk ?? '421.2/084/SDN.PLK.1/2025' }}</small>
                </div>
            @else
                <div style="height: 60px;"></div>
            @endif

            <div class="fw-bold mt-2" style="text-decoration: underline;">
                {{ $approval?->disetujuiOleh?->nama_pengguna ?? 'H. Achmad Zaini, S.Pd., M.Pd.' }}
            </div>
            <div>NIP. 19680512 199303 1 005</div>
        </div>
    </div>
</div>

</body>
</html>

