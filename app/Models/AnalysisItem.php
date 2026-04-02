<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisItem extends Model
{
    protected $table = 't_analysis_items';
    protected $fillable = [
        'section_id', 'item_type', 'tag', 'heading', 'body', 'event_date'
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ReportSection::class, 'section_id');
    }
}
