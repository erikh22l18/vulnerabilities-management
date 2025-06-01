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
            // Drop the simple index on 'title'. Conventionally named 'vulnerabilities_title_index'.
            // If the index name is different, this might need adjustment.
            // It's safer to use $table->dropIndex(['title']); if you only know the column.
            // However, specifying the conventional name is often done.
            if (Schema::hasColumn('vulnerabilities', 'title')) { // Check column exists before dropping index
                // Attempt to find index name if not default, this is complex and DB specific.
                // For this example, we assume the conventional name or that it's the primary way to drop.
                // A more robust way for unknown index names is to list indexes and drop by column.
                // But for typical Laravel setups, 'vulnerabilities_title_index' is standard.
                // $table->dropIndex('vulnerabilities_title_index'); // Standard way
                // Alternative if name is unknown: $table->dropIndex(['title']); (might not work on all DBs for just dropping)
                // Check if index exists before dropping to prevent errors if it was already removed or named differently.
                // This check is not straightforward with Blueprint directly. Usually done by checking DB schema.
                // For simplicity, we'll assume it exists with the conventional name.
                 try {
                    $table->dropIndex('vulnerabilities_title_index');
                 } catch (\Exception $e) {
                    // Log or handle if index doesn't exist, or let it fail if schema is expected to be consistent.
                    // On some DBs, dropping a non-existent index might not error, on others it will.
                    // If title is part of a composite index, this would also fail.
                 }
            }
        });

        Schema::table('vulnerabilities', function (Blueprint $table) {
            // Change column types to TEXT
            // title was NOT NULL originally (default for string), so it should remain NOT NULL.
            // ->change() method preserves existing attributes like nullability unless explicitly changed.
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
            // Change column types back to VARCHAR(255)
            // Assuming original length was 255 for string types.
            $table->string('title', 255)->change(); // This will be NOT NULL by default
            $table->string('description', 255)->nullable()->change();
            $table->string('component', 255)->nullable()->change();
        });

        Schema::table('vulnerabilities', function (Blueprint $table) {
            // Re-add the simple index on 'title'
            if (Schema::hasColumn('vulnerabilities', 'title')) { // Check column exists before adding index
                $table->index('title', 'vulnerabilities_title_index');
            }
        });
    }
};
