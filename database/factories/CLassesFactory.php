<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CLasses>
 */
class CLassesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grade' => fake()->randomElement(['10', '11', '12']),
            'major_id' => Major::factory(),
            'year_id' => SchoolYear::factory(),
            'class_name' => fake()->word(2)
        ];
    }
}
