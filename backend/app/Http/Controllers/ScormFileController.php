<?php

namespace App\Http\Controllers;

use App\Domain\Scorm\Models\ScormPackage;
use Illuminate\Support\Facades\Storage;

class ScormFileController extends Controller
{
    public function show(ScormPackage $scorm, string $path)
    {
        $disk = Storage::disk(config('scorm.scorm_disk'));

        $base = trim($scorm->path, '/');
        $safe = ltrim($path, '/');
        $full = $base . '/' . $safe;

        if (str_contains($safe, '..') || ! $disk->exists($full)) {
            abort(404);
        }

        $mime = $disk->mimeType($full) ?? 'application/octet-stream';

        return response($disk->get($full), 200, [
            'Content-Type' => $mime,
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
