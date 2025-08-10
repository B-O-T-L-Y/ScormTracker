<?php

namespace App\Domain\Scorm\Repositories;

use App\Domain\Scorm\Models\ScormPackage;
use App\Jobs\ExtractScormZipJob;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ScormRepository
{
    public function all(): Collection
    {
        return ScormPackage::with('stats')->latest()->get();
    }

    public function store(UploadedFile $file, string $title): ScormPackage
    {
        $package = ScormPackage::create(['title' => $title, 'path' => '']);
        $disk = Storage::disk(config('scorm.scorm_disk'));
        $tmpPath = 'tmp/' . uniqid() . '_' . $package->id . '.zip';
        $stream = fopen($file->getRealPath(), 'r');
        $disk->put($tmpPath, $stream);
        if (is_resource($stream)) fclose($stream);

        ExtractScormZipJob::dispatch($package->id, $tmpPath)->afterCommit();

        return $package;
    }

    public function delete(ScormPackage $package): void
    {
        Storage::disk(config('scorm.scorm_disk'))->deleteDirectory($package->path);
        $package->delete();
    }
}
