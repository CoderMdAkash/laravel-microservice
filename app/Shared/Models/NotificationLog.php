<?php

declare(strict_types=1);

namespace App\Shared\Models;

class NotificationLog extends TenantBaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'recipient',
        'channel',
        'title',
        'content',
        'status',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}

