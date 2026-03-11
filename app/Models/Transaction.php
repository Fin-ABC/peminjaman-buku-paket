<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'class_id',
        'year_id',
        'semester',
        'transaction_date',
        'is_all_returned',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'is_all_returned' => 'boolean',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'year_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function hasRelatedData(): bool
    {
        return $this->transactionDetails()->exists();
    }

    protected static function booted()
    {
        static::updating(function ($transaction) {
            if ($transaction->isDirty('is_all_returned')) {
                // Jika is_all_returned diubah jadi true
                if ($transaction->is_all_returned) {
                    $transaction->transactionDetails()->update(['status' => 'Returned']);
                } else {
                    // Jika is_all_returned diubah jadi false
                    $transaction->transactionDetails()->update(['status' => 'Borrowed']);
                }
            }
        });
    }

    public function checkAndUpdateAllReturned(): void
    {
        // Cek apakah semua detail sudah 'Returned'
        $allReturned = $this->transactionDetails()
            ->where('status', '!=', 'Returned')
            ->doesntExist();

        $this->updateQuietly(['is_all_returned' => $allReturned]);
    }
}
