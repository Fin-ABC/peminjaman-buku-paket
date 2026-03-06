<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nis'           => fake()->unique()->nik(),
            'student_name'  => fake()->name(),
            'class_id'      => Classes::factory(),
            'entry_year_id' => SchoolYear::factory(),
            'status'        => 'active',
        ];
    }
}
