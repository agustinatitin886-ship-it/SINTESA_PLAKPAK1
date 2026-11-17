<?php

namespace App\Services;

class BwmService
{
    /**
     * Consistency Index (CI) table for BWM based on max preference a_BW (1-9)
     */
    protected static array $ciTable = [
        1 => 0.00,
        2 => 0.44,
        3 => 1.00,
        4 => 1.63,
        5 => 2.30,
        6 => 3.00,
        7 => 3.73,
        8 => 4.47,
        9 => 5.23,
    ];

    /**
     * Calculate BWM Weights using Linear Programming (Simplex Method).
     *
     * @param array $criteria List of criteria objects or arrays with 'id_kriteria'
     * @param int $bestId ID of best criterion
     * @param int $worstId ID of worst criterion
     * @param array $bestToOthers Map: [criterion_id => int 1-9]
     * @param array $othersToWorst Map: [criterion_id => int 1-9]
     * @return array [
     *    'success' => bool,
     *    'weights' => [criterion_id => float],
     *    'xi' => float,
     *    'ci' => float,
     *    'cr' => float,
     *    'is_consistent' => bool,
     *    'message' => string
     * ]
     */
    public function calculate(
        array $criteria,
        int $bestId,
        int $worstId,
        array $bestToOthers,
        array $othersToWorst
    ): array {
        $n = count($criteria);
        if ($n < 2) {
            return [
                'success' => false,
                'message' => 'Jumlah kriteria minimal harus 2.',
            ];
        }

        // Map criteria IDs to 0-based indices
        $idToIndex = [];
        $indexToId = [];
        $idx = 0;
        foreach ($criteria as $c) {
            $id = is_object($c) ? $c->id_kriteria : $c['id_kriteria'];
            $idToIndex[$id] = $idx;
            $indexToId[$idx] = $id;
            $idx++;
        }

        if (!isset($idToIndex[$bestId]) || !isset($idToIndex[$worstId])) {
            return [
                'success' => false,
                'message' => 'Kriteria terbaik atau terburuk tidak valid.',
            ];
        }

        $bestIdx = $idToIndex[$bestId];
        $worstIdx = $idToIndex[$worstId];

        // Format preference vectors
        $aB = [];
        $aW = [];
        foreach ($criteria as $c) {
            $id = is_object($c) ? $c->id_kriteria : $c['id_kriteria'];
            $i = $idToIndex[$id];

            $aB[$i] = (int) ($bestToOthers[$id] ?? 1);
            $aW[$i] = (int) ($othersToWorst[$id] ?? 1);

            if ($id === $bestId) {
                $aB[$i] = 1;
            }
            if ($id === $worstId) {
                $aW[$i] = 1;
            }
        }

        // Variables: w_0, w_1, ..., w_{n-1}, and xi (index n)
        $numVars = $n + 1;
        $c = array_fill(0, $numVars, 0.0);
        $c[$n] = 1.0; // minimize xi

        $A_ub = [];
        $b_ub = [];

        // Constraints for Best-to-others:
        // |w_B - a_{Bj} * w_j| <= xi
        // =>  w_B - a_{Bj} * w_j - xi <= 0
        // => -w_B + a_{Bj} * w_j - xi <= 0
        for ($j = 0; $j < $n; $j++) {
            $row1 = array_fill(0, $numVars, 0.0);
            $row1[$bestIdx] += 1.0;
            $row1[$j] -= $aB[$j];
            $row1[$n] = -1.0;
            $A_ub[] = $row1;
            $b_ub[] = 0.0;

            $row2 = array_fill(0, $numVars, 0.0);
            $row2[$bestIdx] -= 1.0;
            $row2[$j] += $aB[$j];
            $row2[$n] = -1.0;
            $A_ub[] = $row2;
            $b_ub[] = 0.0;
        }

        // Constraints for Others-to-worst:
        // |w_j - a_{jW} * w_W| <= xi
        // =>  w_j - a_{jW} * w_W - xi <= 0
        // => -w_j + a_{jW} * w_W - xi <= 0
        for ($j = 0; $j < $n; $j++) {
            $row3 = array_fill(0, $numVars, 0.0);
            $row3[$j] += 1.0;
            $row3[$worstIdx] -= $aW[$j];
            $row3[$n] = -1.0;
            $A_ub[] = $row3;
            $b_ub[] = 0.0;

            $row4 = array_fill(0, $numVars, 0.0);
            $row4[$j] -= 1.0;
            $row4[$worstIdx] += $aW[$j];
            $row4[$n] = -1.0;
            $A_ub[] = $row4;
            $b_ub[] = 0.0;
        }

        // Equality constraint: sum(w_j) = 1
        $rowEq = array_fill(0, $numVars, 0.0);
        for ($j = 0; $j < $n; $j++) {
            $rowEq[$j] = 1.0;
        }
        $A_eq = [$rowEq];
        $b_eq = [1.0];

        $lpResult = $this->solveLP($c, $A_ub, $b_ub, $A_eq, $b_eq);

        if (!$lpResult['success']) {
            return [
                'success' => false,
                'message' => 'Gagal memecahkan program linier BWM: ' . ($lpResult['message'] ?? 'Tidak ditemukan solusi optimal.'),
            ];
        }

        $solution = $lpResult['x'];
        $xi = max(0.0, (float) $solution[$n]);

        // Weights
        $weights = [];
        $sum = 0.0;
        for ($j = 0; $j < $n; $j++) {
            $w = max(0.0, (float) $solution[$j]);
            $weights[$indexToId[$j]] = $w;
            $sum += $w;
        }

        // Normalize weights if slight floating point drift
        if ($sum > 0) {
            foreach ($weights as $id => $val) {
                $weights[$id] = $val / $sum;
            }
        }

        // Consistency Index & Consistency Ratio
        $maxAbw = max($aB[$worstIdx], $aW[$bestIdx]);
        $ci = self::$ciTable[$maxAbw] ?? 5.23;
        $cr = $ci > 0 ? $xi / $ci : 0.0;

        return [
            'success' => true,
            'weights' => $weights,
            'xi' => round($xi, 5),
            'ci' => $ci,
            'cr' => round($cr, 5),
            'is_consistent' => $cr <= 0.10,
            'aB' => $aB,
            'aW' => $aW,
            'max_abw' => $maxAbw,
            'message' => $cr <= 0.10
                ? 'Perhitungan BWM selesai. Rasio konsistensi sangat baik (CR <= 0.10).'
                : 'Perhitungan BWM selesai, namun rasio konsistensi agak tinggi (CR > 0.10). Disarankan meninjau kembali kuesioner perbandingan.',
        ];
    }

    /**
     * Standard Two-Phase Simplex Solver
     */
    protected function solveLP(array $c, array $A_ub, array $b_ub, array $A_eq = [], array $b_eq = []): array
    {
        $numVars = count($c);
        $numUb = count($b_ub);
        $numEq = count($b_eq);

        $totalCols = $numVars + $numUb + $numEq + 1;
        $totalRows = $numUb + $numEq;

        $tableau = array_fill(0, $totalRows + 2, array_fill(0, $totalCols, 0.0));
        $basic = [];

        // Inequity rows
        for ($i = 0; $i < $numUb; $i++) {
            for ($j = 0; $j < $numVars; $j++) {
                $tableau[$i][$j] = $A_ub[$i][$j];
            }
            $tableau[$i][$numVars + $i] = 1.0;
            $tableau[$i][$totalCols - 1] = $b_ub[$i];
            $basic[$i] = $numVars + $i;
        }

        // Equity rows
        for ($i = 0; $i < $numEq; $i++) {
            $rowIdx = $numUb + $i;
            for ($j = 0; $j < $numVars; $j++) {
                $tableau[$rowIdx][$j] = $A_eq[$i][$j];
            }
            $artCol = $numVars + $numUb + $i;
            $tableau[$rowIdx][$artCol] = 1.0;
            $tableau[$rowIdx][$totalCols - 1] = $b_eq[$i];
            $basic[$rowIdx] = $artCol;
        }

        // Phase 2 Objective row
        for ($j = 0; $j < $numVars; $j++) {
            $tableau[$totalRows][$j] = $c[$j];
        }

        // Phase 1 Objective row
        if ($numEq > 0) {
            for ($i = 0; $i < $numEq; $i++) {
                $rowIdx = $numUb + $i;
                for ($j = 0; $j < $totalCols; $j++) {
                    $tableau[$totalRows + 1][$j] -= $tableau[$rowIdx][$j];
                }
            }

            if (!$this->pivotTableau($tableau, $basic, $totalRows + 1, $totalRows, $totalCols, $numVars + $numUb)) {
                return ['success' => false, 'message' => 'Tahap 1 Simpleks gagal (kendala tidak dapat dipenuhi)'];
            }

            if (abs($tableau[$totalRows + 1][$totalCols - 1]) > 1e-4) {
                return ['success' => false, 'message' => 'Kombinasi preferensi kriteria inkonsisten'];
            }
        }

        // Phase 2: Eliminate basic variables in objective row
        for ($i = 0; $i < $totalRows; $i++) {
            $bCol = $basic[$i];
            $coeff = $tableau[$totalRows][$bCol];
            if (abs($coeff) > 1e-9) {
                for ($j = 0; $j < $totalCols; $j++) {
                    $tableau[$totalRows][$j] -= $coeff * $tableau[$i][$j];
                }
            }
        }

        if (!$this->pivotTableau($tableau, $basic, $totalRows, $totalRows, $totalCols, $numVars + $numUb)) {
            return ['success' => false, 'message' => 'Tahap 2 Simpleks gagal (solusi tidak terbatas)'];
        }

        $x = array_fill(0, $numVars, 0.0);
        for ($i = 0; $i < $totalRows; $i++) {
            if ($basic[$i] < $numVars) {
                $x[$basic[$i]] = $tableau[$i][$totalCols - 1];
            }
        }

        return [
            'success' => true,
            'x' => $x,
            'opt' => -$tableau[$totalRows][$totalCols - 1],
        ];
    }

    protected function pivotTableau(array &$tableau, array &$basic, int $objRow, int $numRows, int $totalCols, int $maxAllowedCol): bool
    {
        $maxIter = 300;
        $iter = 0;

        while ($iter++ < $maxIter) {
            $pivotCol = -1;
            $minVal = -1e-9;
            for ($j = 0; $j < $maxAllowedCol; $j++) {
                if ($tableau[$objRow][$j] < $minVal) {
                    $minVal = $tableau[$objRow][$j];
                    $pivotCol = $j;
                }
            }

            if ($pivotCol === -1) {
                return true;
            }

            $pivotRow = -1;
            $minRatio = INF;
            for ($i = 0; $i < $numRows; $i++) {
                $val = $tableau[$i][$pivotCol];
                if ($val > 1e-9) {
                    $rhs = $tableau[$i][$totalCols - 1];
                    if ($rhs < 0 && $rhs > -1e-9) $rhs = 0;
                    if ($rhs >= 0) {
                        $ratio = $rhs / $val;
                        if ($ratio < $minRatio) {
                            $minRatio = $ratio;
                            $pivotRow = $i;
                        }
                    }
                }
            }

            if ($pivotRow === -1) {
                return false;
            }

            $pivotVal = $tableau[$pivotRow][$pivotCol];
            for ($j = 0; $j < $totalCols; $j++) {
                $tableau[$pivotRow][$j] /= $pivotVal;
            }

            for ($i = 0; $i < count($tableau); $i++) {
                if ($i !== $pivotRow) {
                    $factor = $tableau[$i][$pivotCol];
                    if (abs($factor) > 1e-9) {
                        for ($j = 0; $j < $totalCols; $j++) {
                            $tableau[$i][$j] -= $factor * $tableau[$pivotRow][$j];
                        }
                    }
                }
            }

            $basic[$pivotRow] = $pivotCol;
        }

        return true;
    }
}

