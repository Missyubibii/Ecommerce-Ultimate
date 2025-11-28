<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchTerm extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'term',
        'hits',
        'last_searched_at'
    ];
    protected $casts = ['last_searched_at' => 'datetime'];
}
