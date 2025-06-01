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
        Schema::table('vulnerabilities', function (Blueprint $table) {
            // Change columns to TEXT type to accommodate potentially longer encrypted strings
            $table->text('title')->change();
            $table->text('description')->nullable()->change();
            $table->text('component')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vulnerabilities', function (Blueprint $table) {
            // Reverting to VARCHAR(255). This might cause data truncation if the
            // data stored in TEXT format (especially if encrypted) was longer than 255 characters.
            // This is a simplification; a real rollback might need more sophisticated data handling
            // or accept that the rollback is destructive for oversized data.
            $table->string('title', 255)->change();
            $table->string('description', 255)->nullable()->change();
            $table->string('component', 255)->nullable()->change();
        });
    }
};
