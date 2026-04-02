<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSection extends Model
{
    protected $table = 't_report_sections';
    protected $fillable = [
        'report_id', 'section_num', 'title', 'badge_text', 'content_html'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function analysis_items(): HasMany
    {
        return $this->hasMany(AnalysisItem::class, 'section_id');
    }

    public function charts(): HasMany
    {
        return $this->hasMany(ChartData::class, 'section_id');
    }
}
