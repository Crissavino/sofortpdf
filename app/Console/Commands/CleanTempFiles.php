<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanTempFiles extends Command
{
    protected $signature = 'sofortpdf:clean-temp';

    protected $description = 'Temporäre Dateien löschen, die älter als TEMP_FILE_TTL_HOURS sind';

    public function handle(): int
    {
        $ttlHours = (int) env('TEMP_FILE_TTL_HOURS', 1);
        $threshold = now()->subHours($ttlHours)->getTimestamp();
        $directory = storage_path('app/temp');

        if (!is_dir($directory)) {
            $this->info('Verzeichnis storage/app/temp/ existiert nicht. Nichts zu tun.');
            return 0;
        }

        $deleted = 0;
        $files = glob($directory . '/*');

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $threshold) {
                unlink($file);
                $deleted++;
            }
        }

        $message = "sofortpdf:clean-temp — {$deleted} temporäre Datei(en) gelöscht.";
        $this->info($message);
        Log::info($message);

        return 0;
    }
}
