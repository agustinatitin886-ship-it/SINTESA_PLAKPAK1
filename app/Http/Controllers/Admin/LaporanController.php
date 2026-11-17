<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilWaspas;
use App\Models\Siswa;
use App\Models\ApprovalKepsek;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $daftarPeriode = Siswa::distinct()->pluck('tahun_ajaran')->filter()->sortDesc()->values();
        $daftarKelas = Siswa::distinct()->pluck('kelas')->filter()->sort()->values();

        $selectedPeriode = $request->get('periode', $daftarPeriode->first() ?? '2024/2025');
        $selectedKelas = $request->get('kelas', 'all');

        $query = HasilWaspas::with('siswa')
            ->where('periode', $selectedPeriode);

        if ($selectedKelas && $selectedKelas !== 'all') {
            $query->whereHas('siswa', function ($q) use ($selectedKelas) {
                $q->where('kelas', $selectedKelas);
            });
        }

        $laporan = $query->orderBy('peringkat')->get();
        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.laporan.index', compact(
            'daftarPeriode',
            'daftarKelas',
            'selectedPeriode',
            'selectedKelas',
            'laporan',
            'approval',
            'kriteria'
        ));
    }

    public function cetak(Request $request)
    {
        $selectedPeriode = $request->get('periode', '2024/2025');
        $selectedKelas = $request->get('kelas', 'all');

        $query = HasilWaspas::with(['siswa.penilaian.kriteria'])
            ->where('periode', $selectedPeriode);

        if ($selectedKelas && $selectedKelas !== 'all') {
            $query->whereHas('siswa', function ($q) use ($selectedKelas) {
                $q->where('kelas', $selectedKelas);
            });
        }

        $laporan = $query->orderBy('peringkat')->get();
        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.laporan.cetak', compact(
            'selectedPeriode',
            'selectedKelas',
            'laporan',
            'approval',
            'kriteria'
        ));
    }

    public function perKelas(Request $request)
    {
        $daftarPeriode = Siswa::distinct()->pluck('tahun_ajaran')->filter()->sortDesc()->values();
        $selectedPeriode = $request->get('periode', $daftarPeriode->first() ?? '2024/2025');

        $query = HasilWaspas::with('siswa')
            ->where('periode', $selectedPeriode);

        $semuaHasil = $query->orderBy('peringkat')->get();

        // Group by class and sort each group by peringkat_kelas
        $rekapPerKelas = $semuaHasil->groupBy(function ($item) {
            return $item->siswa->kelas ?? 'Kelas';
        })->sortKeys();

        // Juara Umum Tingkat Sekolah (Skor Qi Tertinggi)
        $juaraUmum = $semuaHasil->first();

        // Juara 1 di Tiap Jenjang Kelas
        $juaraSatuPerKelas = [];
        foreach ($rekapPerKelas as $kls => $siswaList) {
            $juaraSatuPerKelas[$kls] = $siswaList->firstWhere('peringkat_kelas', 1) ?? $siswaList->first();
        }

        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();

        return view('admin.laporan.per_kelas', compact(
            'daftarPeriode',
            'selectedPeriode',
            'rekapPerKelas',
            'juaraUmum',
            'juaraSatuPerKelas',
            'approval'
        ));
    }

    public function cetakPerKelas(Request $request)
    {
        $selectedPeriode = $request->get('periode', '2024/2025');
        $selectedKelas = $request->get('kelas', 'all');

        $query = HasilWaspas::with(['siswa.penilaian.kriteria'])
            ->where('periode', $selectedPeriode);

        if ($selectedKelas && $selectedKelas !== 'all') {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $selectedKelas));
        }

        $semuaHasil = $query->orderBy('peringkat')->get();

        $rekapPerKelas = $semuaHasil->groupBy(function ($item) {
            return $item->siswa->kelas ?? 'Kelas';
        })->sortKeys();

        $juaraUmum = $semuaHasil->first();
        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('admin.laporan.cetak_per_kelas', compact(
            'selectedPeriode',
            'selectedKelas',
            'rekapPerKelas',
            'juaraUmum',
            'approval',
            'kriteria'
        ));
    }

    public function exportExcel(Request $request)
    {
        $selectedPeriode = $request->get('periode', '2024/2025');
        $selectedKelas = $request->get('kelas', 'all');

        $query = HasilWaspas::with(['siswa'])
            ->where('periode', $selectedPeriode);

        if ($selectedKelas && $selectedKelas !== 'all') {
            $query->whereHas('siswa', function ($q) use ($selectedKelas) {
                $q->where('kelas', $selectedKelas);
            });
        }

        $data = $query->orderBy('peringkat')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="Laporan_Siswa_Berprestasi_' . str_replace('/', '-', $selectedPeriode) . '.csv"',
        ];

        return new StreamedResponse(function () use ($data, $selectedPeriode) {
            $output = fopen('php://output', 'w');
            fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel compatibility

            fputcsv($output, ['SD NEGERI PLAKPAK 1 PEGANTENAN']);
            fputcsv($output, ['LAPORAN PENETAPAN SISWA BERPRESTASI']);
            fputcsv($output, ['Metode: Best-Worst Method (BWM) & WASPAS']);
            fputcsv($output, ['Tahun Ajaran / Periode: ' . $selectedPeriode]);
            fputcsv($output, []);

            // Columns
            fputcsv($output, [
                'Peringkat',
                'NISN',
                'Nama Lengkap Siswa',
                'Kelas',
                'Skor WSM (Q1)',
                'Skor WPM (Q2)',
                'Skor Akhir WASPAS (Qi)',
                'Keterangan'
            ]);

            foreach ($data as $item) {
                $keterangan = match ($item->peringkat) {
                    1 => 'Juara I (Prestasi Utama)',
                    2 => 'Juara II',
                    3 => 'Juara III',
                    default => 'Nominasi Berprestasi',
                };

                fputcsv($output, [
                    $item->peringkat,
                    "'" . ($item->siswa->nisn ?? ''),
                    $item->siswa->nama_lengkap ?? '',
                    $item->siswa->kelas ?? '',
                    number_format($item->nilai_wsm, 4, ',', '.'),
                    number_format($item->nilai_wpm, 4, ',', '.'),
                    number_format($item->nilai_qi, 4, ',', '.'),
                    $keterangan,
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }
}

