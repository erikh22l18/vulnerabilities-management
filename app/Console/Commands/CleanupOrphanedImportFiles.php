<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupOrphanedImportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cleanup-orphaned-files
                            {--hours=24 : The maximum age in hours for a temporary file to be kept}
                            {--path=livewire-tmp : The storage path relative to storage/app where temporary files are stored}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes orphaned temporary import files from a specified path that are older than the given number of hours.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $hoursOption = (int) $this->option('hours');
        $pathOption = $this->option('path');

        $directoryPath = storage_path('app/' . $pathOption);

        if (!File::isDirectory($directoryPath)) {
            $this->warn("Directory not found: {$directoryPath}");
            return 1; // Indicate an error
        }

        $files = File::files($directoryPath);

        $deletedFilesCount = 0;
        $checkedFilesCount = 0;
        $now = Carbon::now();

        $this->info("Starting cleanup of orphaned files in '{$directoryPath}' older than {$hoursOption} hours...");

        foreach ($files as $file) {
            $checkedFilesCount++;

            // $file is an SplFileInfo object
            $filePath = $file->getRealPath();

            if (!$filePath) { // Should not happen with File::files unless symlink issues
                $this->warn("Could not get real path for a file object: " . $file->getFilename());
                continue;
            }

            $fileLastModifiedTimestamp = File::lastModified($filePath);
            $fileLastModifiedDate = Carbon::createFromTimestamp($fileLastModifiedTimestamp);

            if ($now->diffInHours($fileLastModifiedDate) > $hoursOption) {
                try {
                    if (File::delete($filePath)) {
                        Log::info("Deleted orphaned import file: " . $filePath);
                        $this->line("Deleted: " . $file->getFilename());
                        $deletedFilesCount++;
                    } else {
                        $this->error("Failed to delete file: " . $file->getFilename());
                        Log::error("Failed to delete orphaned import file: " . $filePath);
                    }
                } catch (\Exception $e) {
                    $this->error("Error deleting file {$file->getFilename()}: " . $e->getMessage());
                    Log::error("Error deleting orphaned import file {$filePath}: " . $e->getMessage());
                }
            }
        }

        $this->info("Cleanup finished. Checked {$checkedFilesCount} files. Deleted {$deletedFilesCount} orphaned import files older than {$hoursOption} hours from '{$directoryPath}'.");

        return 0; // Indicate success
    }
}
