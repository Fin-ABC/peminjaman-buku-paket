<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalStock = fake()->numberBetween(10, 100);
        return [
            'book_code'       => null,
            'title'           => fake()->sentence(4),
            'subject_id'      => Subject::factory(),
            'major_id'        => Major::factory(),
            'grade'           => fake()->randomElement(['10', '11', '12']),
            'semester'        => fake()->randomElement(['odd', 'even']),
            'total_stock'     => $totalStock,
            'remaining_stock' => fake()->numberBetween(0, $totalStock),
        ];
    }
}
