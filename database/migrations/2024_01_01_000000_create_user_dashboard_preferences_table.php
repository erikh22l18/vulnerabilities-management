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
        Schema::create('user_dashboard_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dashboard_type'); // e.g., 'admin', 'lider', 'miembro'
            $table->string('widget_key');      // Unique key for the widget, e.g., 'critical_vulnerabilities'
            $table->boolean('is_visible')->default(true);
            // $table->integer('order')->default(0); // Future use: for widget ordering
            $table->json('settings')->nullable(); // Future use: for widget-specific settings
            $table->timestamps();

            $table->unique(['user_id', 'dashboard_type', 'widget_key'], 'user_dashboard_preference_unique');
            $table->index('dashboard_type');
            $table->index('widget_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_preferences');
    }
};
