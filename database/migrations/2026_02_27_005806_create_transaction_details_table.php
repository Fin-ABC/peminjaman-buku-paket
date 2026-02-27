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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('student_id')->constrained('students', 'id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['Borrowed', 'Returned', 'Overdue']);
            $table->date('return_date');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
