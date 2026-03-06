<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**aaaaaa
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_code')->unique()->nullable();
            $table->string('title');
            $table->foreignId('subject_id')->constrained('subjects', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('major_id')->constrained('majors', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('grade', ['10','11','12']);
            $table->enum('semester', ['odd', 'even']);
            $table->integer('total_stock')->default(0);
            $table->integer('remaining_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
