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
    }
    public function transaction(){
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

        // Hitung buku dengan jenis yang sama
        $sameTypeCount = self::where('subject_id', $this->subject_id)
            ->where('major_id', $this->major_id)
            ->where('grade', $this->grade)
            ->where('semester', $this->semester)
            ->where('id', '<=', $this->id) // Hitung sampai ID ini
            ->count() +1;

        // Format: [ID KESELURUHAN]-[KODE MAPEL]-[TINGKAT]-[KODE JURUSAN]-[SEMESTER]-[ID JENIS SAMA]
        $bookCode =  sprintf(
            '%02d-%s-%s-%s-%s-%02d',
            $this->id,
            $subjectCode,
            $this->grade,
            $majorCode,
            $semesterCode,
            $sameTypeCount
        );

        $this->updateQuietly(['book_code' => $bookCode]);
    }
}
