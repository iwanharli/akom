<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartData extends Model
{
    protected $table = 't_chart_data';
    protected $fillable = [
        'section_id', 'chart_type', 'json_payload'
    ];

    protected $casts = [
        'json_payload' => 'array',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ReportSection::class, 'section_id');
    }
}
