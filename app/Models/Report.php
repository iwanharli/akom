<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Support\Str;

class Report extends Model
{
    protected $table = 't_reports';
    protected $fillable = [
        'uuid', 'user_id', 'title', 'subtitle', 'alert_text', 'report_date', 'classification', 
        'usd_idr_rate', 'brent_oil_price', 'asumsi_icp', 'asumsi_kurs', 'status', 'conclusion_html'
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(ReportStat::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ReportSection::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 't_report_client', 'report_id', 'client_id');
    }
}
