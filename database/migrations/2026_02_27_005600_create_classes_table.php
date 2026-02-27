<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->enum('grade', ['10','11','12']);
            $table->foreignId('major_id')->constrained('majors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('year_id')->constrained('school_years', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('class_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
