<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'major_id',
        'year_id',
        'class_name',
    ];

    public function major(){
        return $this->belongsTo(Major::class);
    }
    public function schoolYear(){
        return $this->belongsTo(SchoolYear::class, 'year_id');
    }
    public function students(){
        return $this->hasMany(Student::class, 'class_id');
    }

    public function hasRelatedData(): bool{
        return $this->students()->exists();
    }
}
