<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriteria = Kriteria::orderBy('kode_kriteria')->get();
        $totalBobot = $kriteria->sum('bobot_bwm');
        return view('admin.kriteria.index', compact('kriteria', 'totalBobot'));
    }

    public function create()
    {
        // Suggest next code
        $count = Kriteria::count() + 1;
        $suggestedCode = 'C' . $count;
        return view('admin.kriteria.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kriteria' => ['required', 'string', 'max:20', 'unique:kriteria,kode_kriteria'],
            'nama_kriteria' => ['required', 'string', 'max:150'],
            'jenis_atribut' => ['required', 'in:Benefit,Cost'],
        ]);

        $validated['bobot_bwm'] = 0.0;

        Kriteria::create($validated);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria baru ' . $validated['nama_kriteria'] . ' berhasil ditambahkan. Silakan hitung ulang bobot pada Kalkulator BWM.');
    }

    public function edit(Kriteria $kriterium)
    {
        return view('admin.kriteria.edit', ['kriteria' => $kriterium]);
    }

    public function update(Request $request, Kriteria $kriterium)
    {
        $validated = $request->validate([
            'kode_kriteria' => ['required', 'string', 'max:20', Rule::unique('kriteria')->ignore($kriterium->id_kriteria, 'id_kriteria')],
            'nama_kriteria' => ['required', 'string', 'max:150'],
            'jenis_atribut' => ['required', 'in:Benefit,Cost'],
        ]);

        $kriterium->update($validated);

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria ' . $kriterium->kode_kriteria . ' berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriterium)
    {
        $kode = $kriterium->kode_kriteria;
        $kriterium->delete();

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Kriteria ' . $kode . ' berhasil dihapus. Jangan lupa hitung ulang bobot pada Kalkulator BWM.');
    }
}

