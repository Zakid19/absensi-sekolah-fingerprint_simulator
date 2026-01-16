<?php

namespace App\Models;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'class_rooms';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = [
        'name',
    ];

    public function students() {
        return $this->hasMany(Student::class, );
    }
}
