<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\HasilWaspas;
use App\Models\ApprovalKepsek;
use App\Models\Siswa;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $daftarPeriode = Siswa::distinct()->pluck('tahun_ajaran')->filter()->sortDesc()->values();
        $selectedPeriode = $request->get('periode', $daftarPeriode->first() ?? '2024/2025');

        $daftarKelas = Siswa::distinct()->pluck('kelas')->filter()->sort()->values();
        $selectedKelas = $request->get('kelas', 'all');

        $query = HasilWaspas::with('siswa')
            ->where('periode', $selectedPeriode);

        if ($selectedKelas && $selectedKelas !== 'all') {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $selectedKelas));
        }

        $hasil = $query->orderBy('peringkat')->get();
        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        return view('kepsek.verifikasi.index', compact(
            'daftarPeriode',
            'selectedPeriode',
            'daftarKelas',
            'selectedKelas',
            'hasil',
            'approval',
            'kriteria'
        ));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'periode' => ['required', 'string'],
            'status' => ['required', 'in:disetujui,ditolak,menunggu'],
            'nomor_sk' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
        ]);

        $periode = $request->periode;
        $status = $request->status;

        $nomorSk = $request->nomor_sk ?: '421.2/' . rand(100, 999) . '/SDN.PLK.1/' . date('Y');

        ApprovalKepsek::updateOrCreate(
            ['periode' => $periode],
            [
                'status' => $status,
                'nomor_sk' => $status === 'disetujui' ? $nomorSk : null,
                'catatan' => $request->catatan,
                'tanggal_persetujuan' => $status === 'disetujui' ? now() : null,
                'disetujui_oleh' => Auth::id(),
            ]
        );

        $msg = $status === 'disetujui'
            ? 'Hasil perankingan siswa berprestasi periode ' . $periode . ' telah BERHASIL DISETUJUI & DISAHKAN secara resmi!'
            : 'Status perankingan telah diperbarui: ' . strtoupper($status) . '.';

        return redirect()->route('kepsek.verifikasi.index', ['periode' => $periode])
            ->with('success', $msg);
    }

    public function skResmi(Request $request)
    {
        $selectedPeriode = $request->get('periode', '2024/2025');
        $approval = ApprovalKepsek::with('disetujuiOleh')->where('periode', $selectedPeriode)->first();

        $hasil = HasilWaspas::with('siswa')
            ->where('periode', $selectedPeriode)
            ->orderBy('peringkat')
            ->get();

        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        // Pengelompokan hasil per tingkat kelas secara otomatis
        $rekapPerKelas = $hasil->groupBy(function($item) {
            return $item->siswa->kelas ?? 'Kelas';
        })->sortKeys();

        $juaraUmum = $hasil->first();

        return view('kepsek.verifikasi.sk', compact('selectedPeriode', 'approval', 'hasil', 'kriteria', 'rekapPerKelas', 'juaraUmum'));
    }
}

