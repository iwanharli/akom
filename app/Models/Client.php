<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 't_clients';

    protected $fillable = [
        'name',
        'institution',
        'email',
        'status',
    ];

    public function reports()
    {
        return $this->belongsToMany(Report::class, 't_report_client', 'client_id', 'report_id');
    }
}
