<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\LogBwm;
use App\Services\BwmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BwmController extends Controller
{
    protected BwmService $bwmService;

    public function __construct(BwmService $bwmService)
    {
        $this->bwmService = $bwmService;
    }

    public function index()
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $latestLog = LogBwm::with(['bestKriteria', 'worstKriteria'])->latest()->first();
        $allLogs = LogBwm::with(['bestKriteria', 'worstKriteria'])->latest()->take(10)->get();

        return view('admin.bwm.index', compact('kriteria', 'latestLog', 'allLogs'));
    }

    public function calculate(Request $request)
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();

        if ($kriteria->count() < 2) {
            return back()->with('error', 'Minimal harus ada 2 kriteria untuk melakukan pembobotan BWM.');
        }

        $request->validate([
            'best_kriteria_id' => ['required', 'exists:kriteria,id_kriteria'],
            'worst_kriteria_id' => ['required', 'exists:kriteria,id_kriteria', 'different:best_kriteria_id'],
            'best_to_others' => ['required', 'array'],
            'others_to_worst' => ['required', 'array'],
            'periode' => ['nullable', 'string'],
        ], [
            'worst_kriteria_id.different' => 'Kriteria Terburuk tidak boleh sama dengan Kriteria Terbaik.',
        ]);

        $bestId = (int) $request->best_kriteria_id;
        $worstId = (int) $request->worst_kriteria_id;
        $bestToOthers = $request->best_to_others;
        $othersToWorst = $request->others_to_worst;
        $periode = $request->periode ?? '2024/2025';

        // Run BWM calculation
        $result = $this->bwmService->calculate(
            $kriteria->all(),
            $bestId,
            $worstId,
            $bestToOthers,
            $othersToWorst
        );

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        $bestKriteria = $kriteria->firstWhere('id_kriteria', $bestId);
        $worstKriteria = $kriteria->firstWhere('id_kriteria', $worstId);

        return view('admin.bwm.preview', [
            'kriteria' => $kriteria,
            'bestKriteria' => $bestKriteria,
            'worstKriteria' => $worstKriteria,
            'bestId' => $bestId,
            'worstId' => $worstId,
            'bestToOthers' => $bestToOthers,
            'othersToWorst' => $othersToWorst,
            'weights' => $result['weights'],
            'xi' => $result['xi'],
            'ci' => $result['ci'],
            'cr' => $result['cr'],
            'isConsistent' => $result['is_consistent'],
            'periode' => $periode,
        ]);
    }

    public function apply(Request $request)
    {
        $request->validate([
            'best_kriteria_id' => ['required', 'exists:kriteria,id_kriteria'],
            'worst_kriteria_id' => ['required', 'exists:kriteria,id_kriteria'],
            'weights' => ['required', 'array'],
            'best_to_others' => ['required', 'string'], // JSON encoded string from preview
            'others_to_worst' => ['required', 'string'],
            'consistency_ratio' => ['required', 'numeric'],
            'xi_value' => ['nullable', 'numeric'],
            'periode' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Update bobot_bwm in kriteria table
            foreach ($request->weights as $idKriteria => $weight) {
                Kriteria::where('id_kriteria', $idKriteria)->update([
                    'bobot_bwm' => (float) $weight,
                ]);
            }

            // 2. Insert into log_bwm
            LogBwm::create([
                'best_kriteria_id' => $request->best_kriteria_id,
                'worst_kriteria_id' => $request->worst_kriteria_id,
                'vektor_best_to_others' => json_decode($request->best_to_others, true),
                'vektor_others_to_worst' => json_decode($request->others_to_worst, true),
                'consistency_ratio' => (float) $request->consistency_ratio,
                'xi_value' => (float) $request->xi_value,
                'periode' => $request->periode ?? '2024/2025',
            ]);
        });

        return redirect()->route('admin.bwm.index')
            ->with('success', 'Bobot kriteria hasil BWM berhasil disimpan dan diterapkan ke seluruh sistem!');
    }
}

