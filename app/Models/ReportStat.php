<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStat extends Model
{
    protected $table = 't_report_stats';
    protected $fillable = [
        'report_id', 'label', 'value', 'delta_text', 'status_level'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}
