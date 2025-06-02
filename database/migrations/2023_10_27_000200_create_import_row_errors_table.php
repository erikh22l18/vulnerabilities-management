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
        Schema::create('import_row_errors', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Using ULID for primary key
            $table->foreignUlid('import_batch_id')->constrained('import_batches')->onDelete('cascade');
            $table->unsignedInteger('row_number'); // Original row number from the file
            $table->json('error_messages'); // Array of error messages for the row
            $table->json('row_data')->nullable(); // Original data of the failed row (as JSON)
            $table->timestamps();

            $table->index('import_batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_row_errors');
    }
};
