<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'book_item_id',
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
    public function book_item(){
        return $this->belongsTo(BookItem::class);
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

    public static function getStatusColor(string $status): string
    {
        return match ($status) {
            'Borrowed' => 'warning',
            'Returned' => 'success',
            'Overdue' => 'danger',
            default => 'gray',
        };
    }

    public function scopeCheckOverdue($query)
    {
        return $query->where('status', 'Borrowed')
            ->whereDate('return_date', '<', now())
            ->update(['status' => 'Overdue']);
    }
}
