<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'major_code',
        'major_name',
    ];

    public function hasRelatedData(): bool
    {
        return $this->classes()->exists() ||
            $this->book()->exists();
    }

    public function classes()
    {
        return $this->hasMany(Classes::class, 'major_id');
    }

    public function book(){
        return $this->hasMany(Book::class, 'major_id');
    }

    public static function generateCode(string $name): string
    {
        $words = explode(' ', $name);

        if (count($words) === 1) {
            return strtoupper(substr($name, 0, min(3, strlen($name))));
        }

        $code = '';
        // Jika lebih dari 1 kata
        $code = '';
        foreach ($words as $word) {
            if (in_array(strtolower($word), ['dan', 'atau', 'serta', 'dengan', '&'])) {
                continue;
            }
            $code .= strtoupper(substr($word, 0, 1));
        }

        return $code;
    }
}
