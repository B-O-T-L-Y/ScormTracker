<?php

namespace App\Filament\Resources\ScormPackageResource\Pages;

use App\Filament\Resources\ScormPackageResource;
use App\Jobs\ExtractScormZipJob;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditScormPackage extends EditRecord
{
    protected static string $resource = ScormPackageResource::class;

    protected function afterSave(): void
    {
        $fileState = data_get($this->data, 'file');
        $filePath  = is_array($fileState) ? (array_values($fileState)[0] ?? null) : $fileState;

        if (! $filePath) {
            return;
        }

        $disk = Storage::disk(config('scorm.scorm_disk'));
        if (! $disk->exists($filePath)) {
            throw new \RuntimeException("Uploaded file not found on disk: {$filePath}");
        }

        $tmpPath = 'tmp/' . uniqid() . '_' . $this->record->id . '.zip';
        $disk->move($filePath, $tmpPath);

        \Log::info('Dispatching ExtractScormZipJob', [
            'package_id' => $this->record->id,
            'tmp' => $tmpPath,
        ]);

        ExtractScormZipJob::dispatch($this->record->id, $tmpPath)->afterCommit();
    }
}
