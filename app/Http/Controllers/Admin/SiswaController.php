<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query()->with('penilaian');

        if ($request->filled('kelas') && $request->kelas !== 'all') {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('tahun_ajaran') && $request->tahun_ajaran !== 'all') {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswa = $query->orderBy('kelas')->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        $daftarKelas = Siswa::distinct()->pluck('kelas')->filter()->sort()->values();
        $daftarTahunAjaran = Siswa::distinct()->pluck('tahun_ajaran')->filter()->sortDesc()->values();
        $totalKriteria = Kriteria::count();

        return view('admin.siswa.index', compact('siswa', 'daftarKelas', 'daftarTahunAjaran', 'totalKriteria'));
    }

    public function create()
    {
        $daftarTahunAjaran = Siswa::distinct()->pluck('tahun_ajaran')->filter()->values();
        return view('admin.siswa.create', compact('daftarTahunAjaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'max:20', 'unique:siswa,nisn'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tahun_ajaran' => ['required', 'string', 'max:30'],
        ]);

        $siswa = Siswa::create($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa ' . $siswa->nama_lengkap . ' berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa')->ignore($siswa->id_siswa, 'id_siswa')],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tahun_ajaran' => ['required', 'string', 'max:30'],
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa ' . $siswa->nama_lengkap . ' berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $nama = $siswa->nama_lengkap;
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa ' . $nama . ' berhasil dihapus.');
    }
}

