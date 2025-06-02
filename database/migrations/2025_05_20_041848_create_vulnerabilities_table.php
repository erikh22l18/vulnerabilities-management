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
        Schema::create('vulnerabilities', function (Blueprint $table) {
            $table->id();

            $table->text('title'); // Changed from string('title')
            $table->date('detection_date')->nullable();
            $table->text('description')->nullable(); // Already text, verified
            $table->text('component')->nullable(); // Changed from string('component')->nullable()
            $table->foreignId('type_id')->nullable()->constrained('vulnerability_types')->nullOnDelete();
            $table->string('owasp_classification')->nullable();

            $table->string('cvss_vector')->nullable();
            $table->decimal('cvss_score', 4, 2)->nullable();

            $table->string('severity_level')->nullable();
            $table->string('exploit_probability')->nullable();
            $table->string('estimated_impact')->nullable();

            $table->enum('state', ['Detectada', 'En tratamiento', 'Resuelta', 'Cerrada'])->default('Detectada');

            $table->string('detection_source')->nullable();
            $table->string('documentation_url')->nullable();

            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('vulnerability_categories')->nullOnDelete();
            $table->timestamp('resolution_deadline')->nullable();

            $table->string('priority')->nullable();

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // $table->index('title'); // Removed index on title
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vulnerabilities');
    }
};
