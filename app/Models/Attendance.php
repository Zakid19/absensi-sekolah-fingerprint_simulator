<?php

namespace App\Models;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'absensi';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'date',
        'time_in',
        'status',
        'is_late',
        'student_id',
    ];

    public function students() {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

}
