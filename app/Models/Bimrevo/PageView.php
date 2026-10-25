<?php

namespace App\Models\Bimrevo;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $table = 'bimrevo_page_views';

    public $timestamps = false;

    protected $fillable = [
        'visitor_hash',
        'path',
        'referrer_host',
        'device',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }
}
