<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogBwm extends Model
{
    use HasFactory;

    protected $table = 'log_bwm';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'best_kriteria_id',
        'worst_kriteria_id',
        'vektor_best_to_others',
        'vektor_others_to_worst',
        'consistency_ratio',
        'xi_value',
        'periode',
    ];

    protected $casts = [
        'vektor_best_to_others' => 'array',
        'vektor_others_to_worst' => 'array',
        'consistency_ratio' => 'float',
        'xi_value' => 'float',
    ];

    public function bestKriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'best_kriteria_id', 'id_kriteria');
    }

    public function worstKriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class, 'worst_kriteria_id', 'id_kriteria');
    }
}

