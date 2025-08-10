<?php

namespace App\Jobs;

use App\Domain\Scorm\Models\ScormPackage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExtractScormZipJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int    $packageId,
        public string $filename,
    ) {}

    public function handle(): void
    {
        $package = ScormPackage::findOrFail($this->packageId);
        $disk = Storage::disk(config('scorm.scorm_disk'));

        $zipPath = $disk->path($this->filename);
        $relativeTarget = config('scorm.scorm_path') . '/' . $package->id;
        $targetPath = $disk->path($relativeTarget);

        if (! file_exists($zipPath)) {
            throw new \RuntimeException("SCORM file not found at: {$zipPath}");
        }

        $size = filesize($zipPath);
        if ($size < 100) {
            throw new \RuntimeException("SCORM file is too small ({$size} bytes) at: {$zipPath}");
        }

        Log::info('ExtractScormZipJob: starting extraction', [
            'zipPath' => $zipPath,
            'size' => $size,
            'md5' => md5_file($zipPath),
            'disk' => config('scorm.scorm_disk'),
            'targetPath' => $targetPath
        ]);

        $disk->makeDirectory($relativeTarget);

        $zip = new ZipArchive();
        $code = $zip->open($zipPath);

        if ($code === true) {
            if (! $zip->extractTo($targetPath)) {
                $zip->close();
                throw new \RuntimeException("Failed to extract SCORM file: {$zipPath}");
            }

            $zip->close();
            $package->update(['path' => $relativeTarget]);
            $disk->delete($this->filename);

            Log::info('ExtractScormZipJob: extraction complete', [
                'package_id' => $package->id,
                'targetPath' => $targetPath
            ]);
            return;
        }

        // --- Расшифровка кода ошибки ---
        $errorMap = [
            ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
            ZipArchive::ER_RENAME => 'Renaming temporary file failed',
            ZipArchive::ER_CLOSE => 'Closing zip archive failed',
            ZipArchive::ER_SEEK => 'Seek error',
            ZipArchive::ER_READ => 'Read error',
            ZipArchive::ER_WRITE => 'Write error',
            ZipArchive::ER_CRC => 'CRC error',
            ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
            ZipArchive::ER_NOENT => 'No such file',
            ZipArchive::ER_EXISTS => 'File already exists',
            ZipArchive::ER_OPEN => 'Can\'t open file',
            ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
            ZipArchive::ER_ZLIB => 'Zlib error',
            ZipArchive::ER_MEMORY => 'Malloc failure',
            ZipArchive::ER_CHANGED => 'Entry has been changed',
            ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
            ZipArchive::ER_EOF => 'Premature EOF',
            ZipArchive::ER_INVAL => 'Invalid argument',
            ZipArchive::ER_NOZIP => 'Not a zip archive',
            ZipArchive::ER_INTERNAL => 'Internal error',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent',
            ZipArchive::ER_REMOVE => 'Can\'t remove file',
            ZipArchive::ER_DELETED => 'Entry has been deleted',
        ];

        $message = $errorMap[$code] ?? 'Unknown error';
        throw new \RuntimeException("Failed to open SCORM file: {$zipPath}. ZipArchive code: {$code} ({$message})");
    }
}
