<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingFingerprint extends Model
{
    protected $fillable = [
        'fingerprint_id',
        'device_ip',
        'detected_at',
        'is_mapped',
        'status',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'is_mapped' => 'boolean',
    ];
}
