<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('damaged_count')->default(0)->after('remaining_stock');
            $table->integer('lost_count')->default(0)->after('damaged_count');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['damaged_count', 'lost_count']);
        });
    }
};
