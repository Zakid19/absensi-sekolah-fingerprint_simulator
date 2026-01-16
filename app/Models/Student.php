<?php

namespace App\Models;
use App\Models\Attendance;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['nis','name', 'class_room_id', 'fingerprint_id'];

    public function classRoom() {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id');
    }

    public function attendances() {

        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

}


