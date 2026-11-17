<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\HasilWaspas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = $user->kelas_binaan ?? 'Kelas 6'; // default or assigned class

        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        $totalKriteria = $kriteriaList->count();

        // Students in assigned class
        $siswaList = Siswa::where('kelas', $kelas)->with('penilaian')->get();
        $totalSiswa = $siswaList->count();

        $sudahDinilai = 0;
        $belumDinilai = 0;

        foreach ($siswaList as $siswa) {
            $gradedCount = $siswa->penilaian->count();
            if ($gradedCount >= $totalKriteria && $totalKriteria > 0) {
                $sudahDinilai++;
            } else {
                $belumDinilai++;
            }
        }

        $periode = Siswa::where('kelas', $kelas)->distinct()->pluck('tahun_ajaran')->first() ?? '2024/2025';

        // Rekap otomatis peringkat siswa khusus kelas ini
        $hasilKelas = HasilWaspas::where('periode', $periode)
            ->whereHas('siswa', fn($q) => $q->where('kelas', $kelas))
            ->with('siswa')
            ->orderBy('peringkat_kelas')
            ->get();

        $juaraKelas = $hasilKelas->take(3);

        return view('walikelas.dashboard', compact(
            'user',
            'kelas',
            'periode',
            'totalSiswa',
            'sudahDinilai',
            'belumDinilai',
            'totalKriteria',
            'kriteriaList',
            'siswaList',
            'hasilKelas',
            'juaraKelas'
        ));
    }
}


