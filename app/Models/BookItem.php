<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookItem extends Model
{
    /** @use HasFactory<\Database\Factories\BookItemFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'item_code',
        'condition',
    ];

    // Relasi
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'book_item_id');
    }

    // Event untuk auto-generate item_code dan update counts
    protected static function booted()
    {
        // Saat book_item dibuat
        static::created(function ($bookItem) {
            // Generate item_code
            $bookItem->generateAndUpdateItemCode();

            // Update counts di book
            $bookItem->book->updateConditionCounts();
        });

        // Saat book_item diupdate (kondisi berubah)
        static::updated(function ($bookItem) {
            $bookItem->book->updateConditionCounts();
        });

        // Saat book_item dihapus
        static::deleted(function ($bookItem) {
            $bookItem->book->updateConditionCounts();
        });
    }

    // Method untuk generate item_code
    public function generateAndUpdateItemCode(): void
    {
        if (!$this->book || !$this->book->book_code) {
            return;
        }

        // Format: [BOOK_CODE]-[ITEM_ID]
        // Contoh: 02-MAT-11-RPL-1-005
        $itemCode = sprintf(
            '%s-%03d',
            $this->book->book_code,
            $this->id
        );

        $this->updateQuietly(['item_code' => $itemCode]);
    }

    // Helper untuk get condition label
    public static function getConditionLabel(string $condition): string
    {
        return match ($condition) {
            'good' => 'Baik',
            'damaged' => 'Rusak',
            'lost' => 'Hilang',
            default => $condition,
        };
    }

    // Helper untuk get condition color
    public static function getConditionColor(string $condition): string
    {
        return match ($condition) {
            'good' => 'success',    // Hijau
            'damaged' => 'warning', // Kuning
            'lost' => 'danger',     // Merah
            default => 'gray',
        };
    }
}
