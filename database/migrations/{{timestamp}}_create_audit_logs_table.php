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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Quién realizó la acción
            $table->string('action'); // Ej: "vulnerability_created", "vulnerability_field_updated"
            $table->text('description'); // Resumen de la acción
            $table->unsignedBigInteger('auditable_id'); // ID del modelo auditado (ej. Vulnerability ID)
            $table->string('auditable_type'); // Clase del modelo auditado (ej. App\Models\Vulnerability)
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->json('old_values')->nullable(); // Valores antiguos de los campos modificados
            $table->json('new_values')->nullable(); // Valores nuevos de los campos modificados
            $table->timestamps();

            $table->index(['auditable_id', 'auditable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
