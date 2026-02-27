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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('class_id')->constrained('classes', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('year_id')->constrained('school_years', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('semester', ['even', 'odd']);
            $table->date('transaction_date');
            $table->boolean('is_all_returned')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
