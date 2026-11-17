<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kriteria;
use App\Models\Siswa;
use App\Models\Penilaian;
use App\Models\HasilWaspas;
use App\Models\ApprovalKepsek;
use App\Models\LogBwm;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalKriteria = Kriteria::count();
        $totalSiswa = Siswa::count();
        $totalUser = User::count();

        // Count students that have complete penilaian
        $siswaIds = Siswa::pluck('id_siswa');
        $kriteriaCount = $totalKriteria;
        $siswaTernilai = 0;

        if ($kriteriaCount > 0) {
            $siswaTernilai = Penilaian::selectRaw('id_siswa, count(distinct id_kriteria) as count_kriteria')
                ->groupBy('id_siswa')
                ->having('count_kriteria', '>=', $kriteriaCount)
                ->count();
        }

        // BWM calculation status
        $bwmLog = LogBwm::latest()->first();
        $totalBobotBwm = Kriteria::sum('bobot_bwm');
        $bwmStatus = ($totalBobotBwm > 0.0001 && $bwmLog) ? 'Sudah Dihitung' : 'Belum Dihitung';

        // WASPAS status
        $totalHasilWaspas = HasilWaspas::count();
        $latestPeriode = Siswa::distinct()->pluck('tahun_ajaran')->first() ?? '2024/2025';
        $approval = ApprovalKepsek::where('periode', $latestPeriode)->first();

        // Chart Data 1: Kriteria & Bobot BWM
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $chartKriteriaLabels = $kriteriaList->map(fn($k) => $k->kode_kriteria . ' - ' . $k->nama_kriteria)->toArray();
        $chartKriteriaWeights = $kriteriaList->map(fn($k) => round($k->bobot_bwm * 100, 2))->toArray();

        // Chart Data 2: Distribusi Siswa per Kelas
        $kelasDistribution = Siswa::selectRaw('kelas, count(*) as count')
            ->groupBy('kelas')
            ->orderBy('kelas')
            ->pluck('count', 'kelas')
            ->toArray();

        // Chart Data 3: Top 5 Siswa Berprestasi
        $topSiswa = HasilWaspas::with('siswa')
            ->orderBy('peringkat')
            ->take(5)
            ->get();

        $chartTopLabels = $topSiswa->map(fn($h) => $h->siswa->nama_lengkap ?? 'N/A')->toArray();
        $chartTopScores = $topSiswa->map(fn($h) => round($h->nilai_qi, 4))->toArray();

        return view('admin.dashboard', compact(
            'totalKriteria',
            'totalSiswa',
            'totalUser',
            'siswaTernilai',
            'bwmStatus',
            'bwmLog',
            'totalHasilWaspas',
            'approval',
            'latestPeriode',
            'chartKriteriaLabels',
            'chartKriteriaWeights',
            'kelasDistribution',
            'topSiswa',
            'chartTopLabels',
            'chartTopScores'
        ));
    }
}

