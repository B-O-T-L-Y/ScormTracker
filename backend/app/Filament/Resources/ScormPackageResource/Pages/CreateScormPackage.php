<?php

namespace App\Filament\Resources\ScormPackageResource\Pages;

use App\Filament\Resources\ScormPackageResource;
use App\Jobs\ExtractScormZipJob;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateScormPackage extends CreateRecord
{
    protected static string $resource = ScormPackageResource::class;

    protected function afterCreate(): void
    {
        $fileState = data_get($this->data, 'file');
        $filePath = is_array($fileState) ? (array_values($fileState)[0] ?? null) : $fileState;

        \Log::info('CreateScormPackage: fileState', [
            'fileState' => $fileState,
            'filePath' => $filePath,
            'is_array' => is_array($fileState),
            'values' => is_array($fileState) ? array_values($fileState) : null,
        ]);

        if (! $filePath) {
            return;
        }

        $disk = Storage::disk(config('scorm.scorm_disk'));

        if (! $disk->exists($filePath)) {
            throw new \RuntimeException("File not found on disk: {$filePath}");
        }

        $tmpPath = 'tmp/' . uniqid() . "_" . $this->record->id .  '.zip';;
        $disk->move($filePath, $tmpPath);

        \Log::info('Dispatching ExtractScormZipJob', [
            'package_id' => $this->record->id,
            'tmp' => $tmpPath,
        ]);

        ExtractScormZipJob::dispatch($this->record->id, $tmpPath)->afterCommit();
    }
}
