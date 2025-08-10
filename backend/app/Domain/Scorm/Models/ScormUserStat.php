<?php

namespace App\Domain\Scorm\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $scorm_package_id
 * @property int $views_count
 * @property string|null $last_viewed_at
 * @property-read \App\Domain\Scorm\Models\ScormPackage|null $scorm
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat whereLastViewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat whereScormPackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScormUserStat whereViewsCount($value)
 * @mixin \Eloquent
 */
class ScormUserStat extends Model
{
    protected $fillable = [
        'user_id',
        'scorm_package_id',
        'views_count',
        'last_viewed_at',
    ];

    public function scorm(): BelongsTo
    {
        return $this->belongsTo(ScormPackage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    public $timestamps = false;
}
