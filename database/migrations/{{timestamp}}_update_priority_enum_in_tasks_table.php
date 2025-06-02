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
        Schema::table('tasks', function (Blueprint $table) {
            // Modify ENUM to include 'critica' and ensure all are lowercase
            // Important: Check if DB driver supports ENUM modification or requires raw SQL.
            // For simplicity, assuming standard change() works. Otherwise, DB::statement might be needed.
            $table->enum('priority', ['baja', 'media', 'alta', 'critica'])->default('media')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert to old definition (omitting 'critica')
            // Ensure this matches the exact previous definition.
            $table->enum('priority', ['baja', 'media', 'alta'])->default('media')->change();
        });
    }
};
