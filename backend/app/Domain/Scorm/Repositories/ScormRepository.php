<?php

namespace App\Domain\Scorm\Repositories;

use App\Domain\Scorm\Models\ScormPackage;
use App\Jobs\ExtractScormZipJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ScormRepository
{
    public function all(): LengthAwarePaginator
    {
        return ScormPackage::query()
            ->with(['stats' => fn ($q) => $q->where('user_id', Auth::id())])
            ->latest()
            ->paginate(12);
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
