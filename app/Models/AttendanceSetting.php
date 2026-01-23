<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $table = 'pengaturan_absensi';

    protected $fillable = [
        'start_time',
        'late_minutes',
    ];
}
