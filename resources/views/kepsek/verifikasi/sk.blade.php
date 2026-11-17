<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan (SK) Siswa Berprestasi - SDN Plakpak 1 Pegantenan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background: #ffffff;
            font-size: 12pt;
            line-height: 1.5;
            padding: 20px;
        }

        .paper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px 50px;
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
            letter-spacing: 0.5px;
        }

        .kop-surat h3 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .kop-surat p {
            margin: 4px 0 0 0;
            font-size: 10pt;
            font-style: italic;
        }

        .sk-title {
            text-align: center;
            margin-bottom: 24px;
        }

        .sk-title h5 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
        }

        .sk-title .sk-nomor {
            font-size: 11pt;
            margin-top: 4px;
        }

        .sk-title .sk-tentang {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .section-row {
            display: flex;
            margin-bottom: 12px;
        }

        .section-label {
            width: 140px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .section-content {
            flex-grow: 1;
            text-align: justify;
        }

        .table-lampiran {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11pt;
        }

        .table-lampiran th, .table-lampiran td {
            border: 1px solid #000000;
            padding: 6px 10px;
        }

        .table-lampiran th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .ttd-container {
            margin-top: 36px;
            display: flex;
            justify-content: flex-end;
        }

        .ttd-box {
            width: 320px;
            text-align: center;
        }

        .digital-stamp {
            border: 2px solid #15803d;
            background: rgba(22, 163, 74, 0.05);
            color: #15803d;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 12px auto;
            font-size: 9pt;
            font-family: sans-serif;
            font-weight: bold;
            display: inline-block;
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
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
        <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen SK (PDF)
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

    <!-- Judul SK -->
    <div class="sk-title">
        <h5>SURAT KEPUTUSAN KEPALA SD NEGERI PLAKPAK 1</h5>
        <div class="sk-nomor">Nomor: {{ $approval?->nomor_sk ?? '421.2/084/SDN.PLK.1/' . date('Y') }}</div>
        <div class="sk-tentang">
            TENTANG<br>
            PENETAPAN SISWA BERPRESTASI<br>
            TAHUN AJARAN {{ $selectedPeriode }}
        </div>
    </div>

    <!-- Menimbang -->
    <div class="section-row">
        <div class="section-label">Menimbang :</div>
        <div class="section-content">
            <ol type="a" class="ps-3 mb-0">
                <li>bahwa dalam rangka memberikan apresiasi dan motivasi belajar bagi peserta didik yang memiliki prestasi unggul di bidang akademik, kehadiran, sikap, dan kedisiplinan;</li>
                <li>bahwa berdasarkan hasil evaluasi terstruktur pada aplikasi SINTESA SDN PLAKPAK 1 menggunakan metode Best-Worst Method (BWM) dan WASPAS (Weighted Aggregated Sum Product Assessment) telah diperoleh perankingan objektif;</li>
                <li>bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan b, perlu menetapkan Surat Keputusan Kepala Sekolah tentang Penetapan Siswa Berprestasi.</li>
            </ol>
        </div>
    </div>

    <!-- Mengingat -->
    <div class="section-row">
        <div class="section-label">Mengingat :</div>
        <div class="section-content">
            <ol class="ps-3 mb-0">
                <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional;</li>
                <li>Peraturan Pemerintah Nomor 17 Tahun 2010 tentang Pengelolaan dan Penyelenggaraan Pendidikan;</li>
                <li>Peraturan Menteri Pendidikan dan Kebudayaan Republik Indonesia Nomor 23 Tahun 2016 tentang Standar Penilaian Pendidikan;</li>
                <li>Program Kerja SD Negeri Plakpak 1 Pegantenan Tahun Ajaran {{ $selectedPeriode }}.</li>
            </ol>
        </div>
    </div>

    <!-- Memperhatikan -->
    <div class="section-row">
        <div class="section-label">Memperhatikan :</div>
        <div class="section-content">
            Hasil rekapitulasi penilaian kurikulum dan verifikasi perankingan multi-kriteria metode BWM dan WASPAS tanggal {{ $approval?->tanggal_persetujuan ? $approval->tanggal_persetujuan->format('d F Y') : date('d F Y') }}.
        </div>
    </div>

    <div class="text-center fw-bold my-3">
        MEMUTUSKAN:
    </div>

    <!-- Menetapkan -->
    <div class="section-row">
        <div class="section-label">Menetapkan :</div>
        <div class="section-content fw-bold">
            KEPUTUSAN KEPALA SD NEGERI PLAKPAK 1 TENTANG PENETAPAN SISWA BERPRESTASI TAHUN AJARAN {{ $selectedPeriode }}.
        </div>
    </div>

    <div class="section-row">
        <div class="section-label">KESATU :</div>
        <div class="section-content">
            Menetapkan nama-nama peserta didik yang tercantum dalam Lampiran Keputusan ini sebagai <strong>Siswa Berprestasi SD Negeri Plakpak 1 Pegantenan</strong> Tahun Ajaran {{ $selectedPeriode }}.
        </div>
    </div>

    <div class="section-row">
        <div class="section-label">KEDUA :</div>
        <div class="section-content">
            Kepada siswa yang dinyatakan berprestasi diberikan piagam penghargaan dan hak mendapatkan pembinaan lanjutan untuk mewakili sekolah pada ajang perlombaan tingkat kecamatan maupun kabupaten.
        </div>
    </div>

    <div class="section-row">
        <div class="section-label">KETIGA :</div>
        <div class="section-content">
            Keputusan ini mulai berlaku sejak tanggal ditetapkan, dan apabila di kemudian hari terdapat kekeliruan akan diperbaiki sebagaimana mestinya.
        </div>
    </div>

    <!-- Signature Block -->
    <div class="ttd-container">
        <div class="ttd-box">
            <div>Ditetapkan di: Pegantenan</div>
            <div>Pada tanggal: {{ $approval?->tanggal_persetujuan ? $approval->tanggal_persetujuan->format('d F Y') : date('d F Y') }}</div>
            <div class="fw-bold mt-2">Kepala SD Negeri Plakpak 1 Pegantenan</div>

            @if($approval && $approval->status === 'disetujui')
                <div class="digital-stamp">
                    <i class="bi bi-shield-fill-check me-1"></i> TANDATANGAN DIGITAL RESMI<br>
                    <span>ID: {{ md5($approval->nomor_sk . $approval->tanggal_persetujuan) }}</span><br>
                    <small>Diverifikasi & Disahkan Kepala Sekolah</small>
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

    <!-- Page Break for Lampiran -->
    <div class="page-break mt-5 pt-4 border-top">
        <div style="font-size: 10pt;" class="mb-3">
            <strong>LAMPIRAN SURAT KEPUTUSAN KEPALA SD NEGERI PLAKPAK 1</strong><br>
            Nomor : {{ $approval?->nomor_sk ?? '421.2/084/SDN.PLK.1/' . date('Y') }}<br>
            Tanggal : {{ $approval?->tanggal_persetujuan ? $approval->tanggal_persetujuan->format('d F Y') : date('d F Y') }}<br>
            Tentang : Penetapan Siswa Berprestasi Tahun Ajaran {{ $selectedPeriode }}
        </div>

        <h6 class="text-center fw-bold mb-3 text-uppercase">
            LAMPIRAN I: DAFTAR JUARA UMUM & JUARA PER TINGKAT KELAS TAHUN AJARAN {{ $selectedPeriode }}
        </h6>

        @if($juaraUmum)
            <!-- Box Juara Umum Sekolah -->
            <div style="border: 2px solid #000; padding: 10px 14px; margin-bottom: 20px; background-color: #fafafa;">
                <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase;">
                    A. JUARA UMUM TINGKAT SEKOLAH (SDN PLAKPAK 1):
                </div>
                <div style="font-size: 12pt; font-weight: bold; margin-top: 4px;">
                    {{ $juaraUmum->siswa->nama_lengkap ?? '-' }} &bull; {{ $juaraUmum->siswa->kelas ?? '-' }}
                </div>
                <div style="font-size: 10pt;">
                    NISN: {{ $juaraUmum->siswa->nisn ?? '-' }} | Skor WASPAS Akhir ($Q_i$): <strong>{{ number_format($juaraUmum->nilai_qi, 4) }}</strong> | Predikat: <strong>Sangat Layak (Juara Umum)</strong>
                </div>
            </div>
        @endif

        <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase; margin-bottom: 8px;">
            B. DAFTAR PERINGKAT DAN JUARA TIAP TINGKAT KELAS:
        </div>

        @if(isset($rekapPerKelas))
            @foreach($rekapPerKelas as $kls => $siswaList)
                <div style="font-weight: bold; font-size: 11pt; margin-top: 14px; margin-bottom: 4px; border-bottom: 1px solid #333;">
                    Tingkat {{ $kls }}
                </div>
                <table class="table-lampiran mb-3">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Juara Kelas</th>
                            <th style="width: 130px;">NISN</th>
                            <th>Nama Lengkap Peserta Didik</th>
                            <th style="width: 90px;">Skor Qi</th>
                            <th style="width: 110px;">Rank Sekolah</th>
                            <th style="width: 170px;">Status Kelayakan</th>
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
                                        Peringkat {{ $item->peringkat_kelas }}
                                    @endif
                                </td>
                                <td class="text-center font-monospace">{{ $item->siswa->nisn ?? '-' }}</td>
                                <td class="fw-bold">{{ $item->siswa->nama_lengkap ?? '-' }}</td>
                                <td class="text-center font-monospace">{{ number_format($item->nilai_qi, 4) }}</td>
                                <td class="text-center font-monospace">#{{ $item->peringkat }}</td>
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
        @endif

        <!-- Signature for Lampiran -->
        <div class="ttd-container">
            <div class="ttd-box">
                <div class="fw-bold">Kepala SD Negeri Plakpak 1 Pegantenan</div>
                <div style="height: 50px;"></div>
                <div class="fw-bold" style="text-decoration: underline;">
                    {{ $approval?->disetujuiOleh?->nama_pengguna ?? 'H. Achmad Zaini, S.Pd., M.Pd.' }}
                </div>
                <div>NIP. 19680512 199303 1 005</div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

