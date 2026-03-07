<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Agar cuman ada 1 tahun yg active
    protected static function booted()
    {
        static::saving(function ($schoolYear) {
            if ($schoolYear->is_active) {
                static::where('id', '!=', $schoolYear->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function hasRelatedData(): bool
    {
        return $this->classes()->exists() ||
            $this->transaction()->exists() ||
            $this->student()->exists();
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'year_id');
    }
    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'year_id');
    }
    public function student()
    {
        return $this->hasMany(Student::class, 'year_id');
    }
}
