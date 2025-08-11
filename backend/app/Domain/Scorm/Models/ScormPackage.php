<?php

namespace App\Domain\Scorm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domain\Scorm\Models\ScormUserStat> $stats
 * @property-read int|null $stats_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormPackage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScormPackage extends Model
{
    protected $fillable = [
        'title',
        'path',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ScormPackage $package) {
            if (! empty($package->path)) {
                Storage::disk(config('scorm.scorm_disk'))->deleteDirectory($package->path);
            }
        });
    }

    public function stats(): HasMany
    {
        return $this->hasMany(ScormUserStat::class);
    }

    public function getUrl(): string
    {
        $disk = Storage::disk(config('scorm.scorm_disk'));
        $base = trim($this->path, '/');

        $candidates = [
            'index_lms.html',
            'index.html',
            'story.html',
            'lms/index_lms.html',
            'html5/index.html',
            'story_content/index.html',
        ];

        foreach ($candidates as $rel) {
            if ($disk->exists($base.'/'.$rel)) {
                return route('scorm.file', [$this, 'path' => $rel]);
            }
        }

        return route('scorm.file', [$this, 'path' => 'index.html']);
    }
}
