<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    protected function getKelas()
    {
        return Auth::user()->kelas_binaan ?? 'Kelas 6';
    }

    public function index(Request $request)
    {
        $kelas = $this->getKelas();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $totalKriteria = $kriteria->count();

        $siswaList = Siswa::where('kelas', $kelas)
            ->with(['penilaian.kriteria'])
            ->orderBy('nama_lengkap')
            ->get();

        return view('walikelas.penilaian.index', compact('kelas', 'kriteria', 'totalKriteria', 'siswaList'));
    }

    public function matriks()
    {
        $kelas = $this->getKelas();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $siswaList = Siswa::where('kelas', $kelas)
            ->with('penilaian')
            ->orderBy('nama_lengkap')
            ->get();

        return view('walikelas.penilaian.matriks', compact('kelas', 'kriteria', 'siswaList'));
    }

    public function storeMatriks(Request $request)
    {
        $request->validate([
            'nilai' => ['required', 'array'],
        ]);

        $nilaiData = $request->nilai; // format: [id_siswa][id_kriteria] => nilai

        DB::transaction(function () use ($nilaiData) {
            foreach ($nilaiData as $idSiswa => $kriteriaValues) {
                foreach ($kriteriaValues as $idKriteria => $val) {
                    if ($val !== null && $val !== '') {
                        Penilaian::updateOrCreate(
                            [
                                'id_siswa' => $idSiswa,
                                'id_kriteria' => $idKriteria,
                            ],
                            [
                                'nilai_mentah' => (float) $val,
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->route('walikelas.penilaian.index')
            ->with('success', 'Seluruh penilaian kelas berhasil disimpan ke dalam sistem!');
    }

    public function edit(Siswa $siswa)
    {
        $kelas = $this->getKelas();
        // Ensure Wali Kelas is only editing their own class students if set
        if (Auth::user()->kelas_binaan && $siswa->kelas !== $kelas) {
            return redirect()->route('walikelas.penilaian.index')
                ->with('error', 'Anda hanya memiliki hak untuk menilai siswa pada ' . $kelas);
        }

        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $penilaianMap = $siswa->penilaian->pluck('nilai_mentah', 'id_kriteria')->toArray();

        return view('walikelas.penilaian.edit', compact('siswa', 'kriteria', 'penilaianMap'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nilai' => ['required', 'array'],
        ]);

        DB::transaction(function () use ($request, $siswa) {
            foreach ($request->nilai as $idKriteria => $val) {
                if ($val !== null && $val !== '') {
                    Penilaian::updateOrCreate(
                        [
                            'id_siswa' => $siswa->id_siswa,
                            'id_kriteria' => $idKriteria,
                        ],
                        [
                            'nilai_mentah' => (float) $val,
                        ]
                    );
                }
            }
        });

        return redirect()->route('walikelas.penilaian.index')
            ->with('success', 'Penilaian untuk ' . $siswa->nama_lengkap . ' berhasil disimpan!');
    }

    public function rekap()
    {
        $kelas = $this->getKelas();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $siswaList = Siswa::where('kelas', $kelas)
            ->with(['penilaian'])
            ->orderBy('nama_lengkap')
            ->get();

        return view('walikelas.penilaian.rekap', compact('kelas', 'kriteria', 'siswaList'));
    }
}

