<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'student_id',
        'status',
        'return_date',
        'note',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Event untuk auto-update is_all_returned di transaction
    protected static function booted()
    {
        static::saved(function ($detail) {
            $detail->transaction->checkAndUpdateAllReturned();
        });

        static::deleted(function ($detail) {
            $detail->transaction->checkAndUpdateAllReturned();
        });
    }
}
