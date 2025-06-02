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
        Schema::create('import_batches', function (Blueprint $table) {
            $table->ulid('id')->primary(); // Using ULID for primary key
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('original_filename');
            $table->enum('status', ['pending', 'processing', 'completed_with_errors', 'completed_successfully', 'failed'])->default('pending');
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('successful_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('error_summary')->nullable(); // For job-level errors (e.g., file processing failed entirely)
            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
