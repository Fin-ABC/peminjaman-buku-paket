<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_code',
        'title',
        'subject_id',
        'major_id',
        'grade',
        'semester',
        'total_stock',
        'remaining_stock',
        'damaged_count',
        'lost_count',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function hasRelatedData(): bool
    {
        return $this->transaction()->exists();
        return $this->bookItems()->exists();
    }
    public function bookItems()
    {
        return $this->hasMany(BookItem::class);
    }
    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'book_id');
    }

    public function generateBookCode(): void
    {
        if (!$this->id) {
            return;
        }

        $subject = $this->subject;
        $major = $this->major;

        if (!$subject || !$major) {
            return;
        }

        $subjectCode = strtoupper($subject->subject_code);
        $majorCode = strtoupper($major->major_code);
        $semesterCode = $this->semester === 'odd' ? '1' : '2';

        // Format: [ID KESELURUHAN]-[KODE MAPEL]-[TINGKAT]-[KODE JURUSAN]-[SEMESTER]
        $bookCode =  sprintf(
            '%02d-%s-%s-%s-%s',
            $this->id,
            $subjectCode,
            $this->grade,
            $majorCode,
            $semesterCode,
        );

        $this->updateQuietly(['book_code' => $bookCode]);
    }

    public function updateConditionCounts(): void
    {
        $this->updateQuietly([
            'total_stock' => $this->bookItems()->count(),
            'remaining_stock' => $this->bookItems()->where('condition', 'good')->count(),
            'damaged_count' => $this->bookItems()->where('condition', 'damaged')->count(),
            'lost_count' => $this->bookItems()->where('condition', 'lost')->count(),
        ]);
    }

    // Method untuk auto-create book_items saat total_stock bertambah
    public function syncBookItems(int $newTotalStock): void
    {
        $currentCount = $this->bookItems()->count();
        $difference = $newTotalStock - $currentCount;

        if ($difference > 0) {
            // Tambah book_items baru
            for ($i = 0; $i < $difference; $i++) {
                $this->bookItems()->create([
                    'condition' => 'good',
                ]);
            }
        } elseif ($difference < 0) {
            // Hapus book_items (prioritas: good dulu, baru damaged, baru lost)
            $toDelete = abs($difference);

            // Hapus yang good dulu
            $goodItems = $this->bookItems()->where('condition', 'good')->limit($toDelete)->get();
            foreach ($goodItems as $item) {
                $item->delete();
                $toDelete--;
                if ($toDelete <= 0) break;
            }

            // Kalau masih kurang, hapus damaged
            if ($toDelete > 0) {
                $damagedItems = $this->bookItems()->where('condition', 'damaged')->limit($toDelete)->get();
                foreach ($damagedItems as $item) {
                    $item->delete();
                    $toDelete--;
                    if ($toDelete <= 0) break;
                }
            }

            // Kalau masih kurang, hapus lost
            if ($toDelete > 0) {
                $lostItems = $this->bookItems()->where('condition', 'lost')->limit($toDelete)->get();
                foreach ($lostItems as $item) {
                    $item->delete();
                    $toDelete--;
                    if ($toDelete <= 0) break;
                }
            }
        }

        $this->updateConditionCounts();
    }
}
