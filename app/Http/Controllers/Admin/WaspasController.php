<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\Siswa;
use App\Models\HasilWaspas;
use App\Models\ApprovalKepsek;
use App\Services\WaspasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaspasController extends Controller
{
    protected WaspasService $waspasService;

    public function __construct(WaspasService $waspasService)
    {
        $this->waspasService = $waspasService;
    }

    public function index(Request $request)
    {
        $daftarPeriode = Siswa::distinct()->pluck('tahun_ajaran')->filter()->sortDesc()->values();
        $daftarKelas = Siswa::distinct()->pluck('kelas')->filter()->sort()->values();

        $selectedPeriode = $request->get('periode', $daftarPeriode->first() ?? '2024/2025');
        $selectedKelas = $request->get('kelas', 'all');
        $lambda = (float) $request->get('lambda', 0.5);

        // Check if BWM is calculated
        $totalBobot = Kriteria::sum('bobot_bwm');
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();

        $calculation = null;
        $error = null;

        if ($totalBobot <= 0.0001) {
            $error = 'Bobot kriteria BWM belum dihitung atau masih 0. Harap hitung bobot kriteria pada menu Kalkulator BWM terlebih dahulu.';
        } else {
            $calculation = $this->waspasService->calculate($selectedPeriode, $lambda, $selectedKelas);
            if (!$calculation['success']) {
                $error = $calculation['message'];
                $calculation = null;
            }
        }

        // Check if there are already saved results in hasil_waspas for this period
        $savedResults = HasilWaspas::where('periode', $selectedPeriode)->count();
        $approval = ApprovalKepsek::where('periode', $selectedPeriode)->first();

        return view('admin.waspas.index', compact(
            'daftarPeriode',
            'daftarKelas',
            'selectedPeriode',
            'selectedKelas',
            'lambda',
            'kriteriaList',
            'totalBobot',
            'calculation',
            'error',
            'savedResults',
            'approval'
        ));
    }

    public function save(Request $request)
    {
        $request->validate([
            'periode' => ['required', 'string'],
            'lambda' => ['required', 'numeric', 'between:0,1'],
        ]);

        $periode = $request->periode;
        $lambda = (float) $request->lambda;

        $calc = $this->waspasService->calculate($periode, $lambda, null);

        if (!$calc['success'] || empty($calc['results'])) {
            return back()->with('error', 'Gagal memproses perankingan: ' . ($calc['message'] ?? 'Data tidak valid.'));
        }

        DB::transaction(function () use ($periode, $calc) {
            // Delete existing ranking for this period
            HasilWaspas::where('periode', $periode)->delete();

            // Insert new rankings with class-level ranking and eligibility status
            foreach ($calc['results'] as $res) {
                HasilWaspas::create([
                    'id_siswa' => $res['id_siswa'],
                    'periode' => $periode,
                    'nilai_wsm' => $res['wsm'],
                    'nilai_wpm' => $res['wpm'],
                    'nilai_qi' => $res['qi'],
                    'peringkat' => $res['peringkat'],
                    'peringkat_kelas' => $res['peringkat_kelas'] ?? 1,
                    'status_kelayakan' => $res['status_kelayakan'] ?? 'Kandidat Berprestasi',
                ]);
            }

            // Create or reset Approval Kepsek status
            ApprovalKepsek::updateOrCreate(
                ['periode' => $periode],
                [
                    'status' => 'menunggu',
                    'catatan' => 'Hasil perankingan WASPAS telah diperbarui oleh Admin dan siap diverifikasi oleh Kepala Sekolah.',
                    'tanggal_persetujuan' => null,
                    'disetujui_oleh' => null,
                ]
            );
        });

        return redirect()->route('admin.waspas.index', ['periode' => $periode, 'lambda' => $lambda])
            ->with('success', 'Hasil perankingan WASPAS periode ' . $periode . ' berhasil disimpan dan diajukan ke Kepala Sekolah untuk verifikasi & persetujuan!');
    }
}

