<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOrphanedImportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-orphaned-import-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans up orphaned temporary files from the vulnerability import process.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directory = 'temp_imports'; // As per task: storage/app/temp_imports
        $disk = 'local'; // As per task: 'local' disk

        $this->info("Checking for orphaned files in 'storage/app/{$directory}' older than 24 hours...");
        Log::info("Starting cleanup of orphaned import files in 'storage/app/{$directory}'...");

        if (!Storage::disk($disk)->exists($directory)) {
            $this->info("Directory '{$directory}' does not exist on disk '{$disk}'. Nothing to do.");
            Log::info("Directory '{$directory}' does not exist on disk '{$disk}'. Cleanup skipped.");
            return Command::SUCCESS;
        }

        $files = Storage::disk($disk)->files($directory);
        $threshold = Carbon::now()->subHours(24)->getTimestamp();
        $deletedCount = 0;
        $checkedCount = count($files);

        foreach ($files as $file) {
            // $file path is relative to the disk's root, e.g., 'temp_imports/filename.xlsx'
            if (Storage::disk($disk)->lastModified($file) < $threshold) {
                Storage::disk($disk)->delete($file);
                $deletedCount++;
                $this->line("Deleted: {$file}");
                Log::info("Deleted orphaned import file: {$file}");
            }
        }

        $this->info("Cleanup complete. Checked {$checkedCount} files in 'storage/app/{$directory}', deleted {$deletedCount} orphaned files.");
        Log::info("Orphaned import file cleanup finished. Checked: {$checkedCount} in 'storage/app/{$directory}', Deleted: {$deletedCount}.");

        return Command::SUCCESS;
    }
}
