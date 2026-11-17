<?php

namespace App\Services;

use App\Models\Kriteria;
use App\Models\Siswa;
use App\Models\Penilaian;

class WaspasService
{
    /**
     * Perform WASPAS calculation for a specific academic period.
     *
     * @param string|null $periode Academic period (e.g. '2024/2025')
     * @param float $lambda WASPAS weight aggregation parameter (0.0 to 1.0, default 0.5)
     * @param string|null $kelas Optional class filter
     * @return array Detailed calculation results and matrices
     */
    public function calculate(?string $periode = null, float $lambda = 0.5, ?string $kelas = null): array
    {
        $kriteriaList = Kriteria::orderBy('kode_kriteria')->get();
        if ($kriteriaList->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Belum ada data kriteria yang tersimpan.',
            ];
        }

        // Check if BWM weights have been calculated
        $totalWeight = $kriteriaList->sum('bobot_bwm');
        if ($totalWeight <= 0.0001) {
            return [
                'success' => false,
                'message' => 'Bobot kriteria BWM belum dihitung atau masih 0. Harap hitung bobot kriteria pada menu Kalkulator BWM terlebih dahulu.',
            ];
        }

        // Query students
        $siswaQuery = Siswa::query();
        if ($periode) {
            $siswaQuery->where('tahun_ajaran', $periode);
        }
        if ($kelas && $kelas !== 'all') {
            $siswaQuery->where('kelas', $kelas);
        }
        $siswaList = $siswaQuery->with('penilaian')->get();

        if ($siswaList->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ditemukan data siswa untuk periode / kelas yang dipilih.',
            ];
        }

        // Build Decision Matrix X
        // Rows: siswa, Cols: kriteria
        $matrixX = [];
        $colValues = [];

        foreach ($kriteriaList as $k) {
            $colValues[$k->id_kriteria] = [];
        }

        foreach ($siswaList as $siswa) {
            $row = [];
            foreach ($kriteriaList as $k) {
                $penilaian = $siswa->penilaian->firstWhere('id_kriteria', $k->id_kriteria);
                $val = $penilaian ? (float) $penilaian->nilai_mentah : 0.0;
                $row[$k->id_kriteria] = $val;
                $colValues[$k->id_kriteria][] = $val;
            }
            $matrixX[$siswa->id_siswa] = [
                'siswa' => $siswa,
                'values' => $row,
            ];
        }

        // Calculate Min and Max for each criterion column
        $colMax = [];
        $colMin = [];
        foreach ($kriteriaList as $k) {
            $vals = $colValues[$k->id_kriteria];
            $colMax[$k->id_kriteria] = !empty($vals) ? max($vals) : 0.0;
            $colMin[$k->id_kriteria] = !empty($vals) ? min($vals) : 0.0;
        }

        // Normalized Matrix R
        $matrixR = [];
        foreach ($matrixX as $idSiswa => $item) {
            $normRow = [];
            foreach ($kriteriaList as $k) {
                $x = $item['values'][$k->id_kriteria];
                $type = $k->jenis_atribut; // 'Benefit' or 'Cost'
                $maxVal = $colMax[$k->id_kriteria];
                $minVal = $colMin[$k->id_kriteria];

                if (strcasecmp($type, 'Benefit') === 0) {
                    $r = ($maxVal > 0) ? ($x / $maxVal) : 0.0;
                } else {
                    // Cost attribute: min / x
                    if ($x <= 0) {
                        $r = 1.0; // perfect cost score if 0 violations
                    } else {
                        $effectiveMin = ($minVal <= 0) ? 1.0 : $minVal;
                        $r = $effectiveMin / $x;
                        if ($r > 1.0) $r = 1.0;
                    }
                }
                $normRow[$k->id_kriteria] = round($r, 6);
            }
            $matrixR[$idSiswa] = [
                'siswa' => $item['siswa'],
                'values' => $normRow,
            ];
        }

        // Calculate WSM, WPM, and Qi
        $results = [];
        foreach ($matrixR as $idSiswa => $item) {
            $siswa = $item['siswa'];
            $normRow = $item['values'];

            $wsm = 0.0;
            $wpm = 1.0;

            foreach ($kriteriaList as $k) {
                $r = $normRow[$k->id_kriteria];
                $w = (float) $k->bobot_bwm;

                // WSM: sum(r_ij * w_j)
                $wsm += ($r * $w);

                // WPM: prod(r_ij ^ w_j)
                // Avoid pow(0, w) which produces 0 for entire product; use safe small epsilon if r is 0
                $safeR = max($r, 0.0001);
                $wpm *= pow($safeR, $w);
            }

            // WASPAS Qi = lambda * WSM + (1 - lambda) * WPM
            $qi = ($lambda * $wsm) + ((1.0 - $lambda) * $wpm);

            $results[] = [
                'id_siswa' => $siswa->id_siswa,
                'nisn' => $siswa->nisn,
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas' => $siswa->kelas,
                'tahun_ajaran' => $siswa->tahun_ajaran,
                'raw_values' => $matrixX[$idSiswa]['values'],
                'norm_values' => $normRow,
                'wsm' => round($wsm, 6),
                'wpm' => round($wpm, 6),
                'qi' => round($qi, 6),
            ];
        }

        // Sort descending by Qi
        usort($results, function ($a, $b) {
            if ($b['qi'] == $a['qi']) {
                // Secondary tie-breaker: WSM
                return $b['wsm'] <=> $a['wsm'];
            }
            return $b['qi'] <=> $a['qi'];
        });

        // Assign overall rankings & class-level rankings automatically
        $classCounters = [];
        $rank = 1;
        foreach ($results as &$res) {
            $res['peringkat'] = $rank++;
            $kls = $res['kelas'];
            if (!isset($classCounters[$kls])) {
                $classCounters[$kls] = 1;
            } else {
                $classCounters[$kls]++;
            }
            $rankKelas = $classCounters[$kls];
            $res['peringkat_kelas'] = $rankKelas;

            if ($res['peringkat'] === 1 && $rankKelas === 1) {
                $res['status_kelayakan'] = 'Sangat Layak — Juara Umum & Juara 1 ' . $kls;
            } elseif ($rankKelas === 1) {
                $res['status_kelayakan'] = 'Sangat Layak — Juara 1 ' . $kls;
            } elseif ($rankKelas === 2) {
                $res['status_kelayakan'] = 'Layak — Juara 2 ' . $kls;
            } elseif ($rankKelas === 3) {
                $res['status_kelayakan'] = 'Layak — Juara 3 ' . $kls;
            } else {
                $res['status_kelayakan'] = 'Kandidat Berprestasi ' . $kls;
            }
        }
        unset($res);

        // Group results by class for automated recap
        $resultsPerKelas = [];
        foreach ($results as $res) {
            $resultsPerKelas[$res['kelas']][] = $res;
        }
        ksort($resultsPerKelas);

        return [
            'success' => true,
            'kriteria' => $kriteriaList,
            'col_max' => $colMax,
            'col_min' => $colMin,
            'matrix_x' => $matrixX,
            'matrix_r' => $matrixR,
            'results' => $results,
            'results_per_kelas' => $resultsPerKelas,
            'lambda' => $lambda,
            'total_siswa' => count($results),
            'periode' => $periode,
        ];
    }
}

