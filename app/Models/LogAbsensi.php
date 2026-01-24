<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Model;

class LogAbsensi extends Model
{
    protected $table = 'log_absensis';

    protected $fillable = [
        'student_id',
        'attendance_id',
        'waktu_scan',
        'jenis',
        'status',
        'keterangan',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
