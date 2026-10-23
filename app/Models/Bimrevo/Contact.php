<?php

namespace App\Models\Bimrevo;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'bimrevo_contacts';

    public const PLANS = ['Solo', 'Team', 'Enterprise'];

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_HANDLED = 'handled';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_HANDLED => 'Handled',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'plan',
        'message',
        'source_url',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
        'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
        ];
    }
}
