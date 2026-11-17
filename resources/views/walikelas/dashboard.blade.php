@extends('layouts.app')

@section('title', 'Dasbor Wali Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('walikelas.dashboard') }}" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dasbor Wali Kelas</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-0">Dasbor Penilaian Siswa: {{ $kelas }}</h4>
    </div>
    <a href="{{ route('walikelas.penilaian.matriks') }}" class="btn btn-primary">
        <i class="bi bi-table me-1"></i> Formulir Matriks Kelas
    </a>
</div>

<!-- Hasil Perankingan Otomatis Kelas Ini (Tanpa Rekap Manual) -->
@if(isset($hasilKelas) && $hasilKelas->isNotEmpty())
    <div class="card mb-4 border-start border-4 border-start-success">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <div>
                <span class="fw-bold text-dark"><i class="bi bi-trophy-fill text-warning me-1"></i> Rekap Otomatis Siswa Berprestasi {{ $kelas }} (Tahun Ajaran {{ $periode }})</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle ms-2">Otomatis Terkalkulasi</span>
            </div>
            <a href="{{ route('admin.laporan.cetak_per_kelas', ['periode' => $periode, 'kelas' => $kelas]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Cetak Lembar Juara {{ $kelas }}
            </a>
        </div>
        <div class="card-body">
            <!-- 3 Besar Juara Kelas Ini -->
            <div class="row g-3 mb-3">
                @foreach($juaraKelas as $jk)
                    <div class="col-md-4">
                        <div class="p-3 border rounded text-center h-100 bg-white shadow-sm">
                            <div class="mb-2">
                                @if($jk->peringkat_kelas == 1)
                                    <span class="badge bg-warning text-dark border px-3 py-1 fw-bold">JUARA 1 KELAS</span>
                                @elseif($jk->peringkat_kelas == 2)
                                    <span class="badge bg-secondary text-white px-3 py-1 fw-bold">JUARA 2 KELAS</span>
                                @elseif($jk->peringkat_kelas == 3)
                                    <span class="badge text-white px-3 py-1 fw-bold" style="background-color: #b45309;">JUARA 3 KELAS</span>
                                @endif
                            </div>
                            <h6 class="fw-bold text-dark mb-1">{{ $jk->siswa->nama_lengkap ?? '-' }}</h6>
                            <div class="text-muted small mb-2">NISN: <code>{{ $jk->siswa->nisn ?? '-' }}</code></div>
                            <div class="fs-4 fw-bold font-monospace text-success">{{ number_format($jk->nilai_qi, 4) }}</div>
                            <div class="text-muted small" style="font-size: 11px;">Skor WASPAS (Qi) &bull; Peringkat Sekolah: #{{ $jk->peringkat }}</div>
                            <div class="mt-2">
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 11px;">
                                    {{ $jk->status_kelayakan }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tabel Seluruh Peringkat Siswa di Kelas Ini -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle text-center mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 100px;">Peringkat Kelas</th>
                            <th style="width: 130px;">NISN</th>
                            <th class="text-start">Nama Peserta Didik</th>
                            <th style="width: 90px;">Skor WSM</th>
                            <th style="width: 90px;">Skor WPM</th>
                            <th style="width: 100px;">Skor WASPAS</th>
                            <th style="width: 110px;">Peringkat Sekolah</th>
                            <th style="width: 200px;">Status Kelayakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hasilKelas as $hk)
                            <tr>
                                <td class="fw-bold">
                                    @if($hk->peringkat_kelas <= 3)
                                        <span class="badge {{ $hk->peringkat_kelas == 1 ? 'bg-warning text-dark' : ($hk->peringkat_kelas == 2 ? 'bg-secondary text-white' : 'text-white') }}" style="{{ $hk->peringkat_kelas == 3 ? 'background-color: #b45309;' : '' }}">
                                            Juara {{ $hk->peringkat_kelas }}
                                        </span>
                                    @else
                                        <span class="text-muted">{{ $hk->peringkat_kelas }}</span>
                                    @endif
                                </td>
                                <td><code>{{ $hk->siswa->nisn ?? '-' }}</code></td>
                                <td class="text-start fw-medium text-dark">{{ $hk->siswa->nama_lengkap ?? '-' }}</td>
                                <td class="font-monospace text-muted">{{ number_format($hk->nilai_wsm, 4) }}</td>
                                <td class="font-monospace text-muted">{{ number_format($hk->nilai_wpm, 4) }}</td>
                                <td class="font-monospace fw-bold text-success">{{ number_format($hk->nilai_qi, 4) }}</td>
                                <td class="font-monospace text-secondary">#{{ $hk->peringkat }}</td>
                                <td>
                                    @if($hk->peringkat_kelas <= 3)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            {{ $hk->status_kelayakan }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Kandidat Berprestasi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-primary border">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="kpi-value">{{ $totalSiswa }}</div>
                <div class="kpi-label">Siswa Binaan ({{ $kelas }})</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-success border">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="kpi-value text-success">{{ $sudahDinilai }}</div>
                <div class="kpi-label">Siswa Selesai Lengkap</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="kpi-card">
            <div class="kpi-icon bg-light text-warning border">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="kpi-value {{ $belumDinilai > 0 ? 'text-warning' : 'text-muted' }}">{{ $belumDinilai }}</div>
                <div class="kpi-label">Siswa Belum Selesai</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Daftar Kriteria -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                Kriteria Penilaian yang Berlaku
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($kriteriaList as $k)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <div>
                                <span class="font-monospace fw-bold me-2">{{ $k->kode_kriteria }}</span>
                                <span class="text-dark">{{ $k->nama_kriteria }}</span>
                            </div>
                            <span class="badge {{ $k->jenis_atribut === 'Benefit' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} border" style="font-size: 11px;">
                                {{ $k->jenis_atribut }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Roster Siswa -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar Siswa {{ $kelas }}</span>
                <a href="{{ route('walikelas.penilaian.index') }}" class="small text-decoration-none">Kelola Nilai &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 120px;">NISN</th>
                            <th>Nama Lengkap</th>
                            <th class="text-center" style="width: 110px;">Status Nilai</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswaList as $s)
                            @php
                                $graded = $s->penilaian->count();
                                $complete = ($totalKriteria > 0 && $graded >= $totalKriteria);
                            @endphp
                            <tr>
                                <td><code>{{ $s->nisn }}</code></td>
                                <td class="fw-medium text-dark">{{ $s->nama_lengkap }}</td>
                                <td class="text-center">
                                    @if($complete)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Lengkap</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('walikelas.penilaian.edit', $s->id_siswa) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;">
                                        Nilai
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Belum ada siswa pada kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
