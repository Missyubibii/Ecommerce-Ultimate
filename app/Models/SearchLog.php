<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Models\User;

class SearchLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'session_id',
        'keyword',
        'results_count',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value);
    }
}
