<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'student_name',
        'class_id',
        'status',
    ];

    protected $casts = ['status' => 'string'];
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function hasRelatedData(): bool{
        return false;
    }

    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            'active' => 'Aktif',
            'graduated' => 'Lulus',
            'move' => 'Pindah',
            'dropout' => 'DO',
            default => $status,
        };
    }

    public static function getStatusColor(string $status): string
    {
        return match($status) {
            'active' => 'success',
            'graduated' => 'info',
            'move' => 'warning',
            'dropout' => 'danger',
            default => 'gray',
        };
    }
}
