<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\HasilWaspas;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\ApprovalKepsek;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $periode = Siswa::distinct()->pluck('tahun_ajaran')->first() ?? '2024/2025';
        $approval = ApprovalKepsek::where('periode', $periode)->first();

        $totalSiswa = Siswa::count();
        $totalKelas = Siswa::distinct('kelas')->count();
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        // Top 3 Podium Juara Umum Sekolah
        $top3 = HasilWaspas::with('siswa')
            ->where('periode', $periode)
            ->orderBy('peringkat')
            ->take(3)
            ->get();

        $highestScore = $top3->first()?->nilai_qi ?? 0;

        // Rekap Otomatis Juara 1 di Setiap Jenjang Kelas (Tanpa Rekap Manual)
        $semuaHasil = HasilWaspas::with('siswa')
            ->where('periode', $periode)
            ->orderBy('peringkat')
            ->get();

        $rekapPerKelas = $semuaHasil->groupBy(fn($i) => $i->siswa->kelas ?? 'Kelas')->sortKeys();
        $juaraPerKelas = [];
        foreach ($rekapPerKelas as $kls => $sList) {
            $juaraPerKelas[$kls] = $sList->firstWhere('peringkat_kelas', 1) ?? $sList->first();
        }

        // Chart 1: Performa Nilai Rata-rata per Kelas
        $kelasLabels = Siswa::distinct()->pluck('kelas')->sort()->values();
        $kelasAvgScores = [];
        foreach ($kelasLabels as $k) {
            $avgQi = HasilWaspas::where('periode', $periode)
                ->whereHas('siswa', fn($q) => $q->where('kelas', $k))
                ->avg('nilai_qi');
            $kelasAvgScores[] = round(($avgQi ?? 0) * 100, 2);
        }

        // Chart 2: Rata-rata Ketercapaian Kriteria (0 - 100)
        $kriteriaLabels = [];
        $kriteriaAverages = [];
        foreach ($kriteriaList as $k) {
            $avg = Penilaian::where('id_kriteria', $k->id_kriteria)->avg('nilai_mentah');
            $kriteriaLabels[] = $k->kode_kriteria . ' (' . $k->nama_kriteria . ')';
            $kriteriaAverages[] = round($avg ?? 0, 2);
        }

        return view('kepsek.dashboard', compact(
            'periode',
            'approval',
            'totalSiswa',
            'totalKelas',
            'top3',
            'highestScore',
            'juaraPerKelas',
            'kelasLabels',
            'kelasAvgScores',
            'kriteriaLabels',
            'kriteriaAverages'
        ));
    }
}

