<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Str;

class Report extends Model
{
    protected $fillable = [
        'uuid', 'title', 'subtitle', 'alert_text', 'report_date', 'classification', 
        'usd_idr_rate', 'brent_oil_price', 'asumsi_icp', 'asumsi_kurs', 'status'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($report) {
            $report->uuid = (string) Str::uuid();
        });
    }

    protected $casts = [
        'report_date' => 'date',
        'usd_idr_rate' => 'decimal:2',
        'brent_oil_price' => 'decimal:2',
    ];

    public function stats(): HasMany
    {
        return $this->hasMany(ReportStat::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ReportSection::class);
    }
}
