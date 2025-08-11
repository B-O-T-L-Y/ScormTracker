<?php

namespace App\Http\Controllers;

use App\Domain\Scorm\Models\ScormPackage;
use App\Domain\Scorm\Models\ScormUserStat;
use App\Domain\Scorm\Repositories\ScormRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ScormController extends Controller
{
    public function index(ScormRepository $repository): View
    {
        $packages = $repository->all();

        return view('scorm.index', compact('packages'));
    }

    public function show(ScormPackage $scorm): View
    {
        $user = Auth::user();

        $stat = ScormUserStat::firstOrCreate(
            ['user_id' => $user->id, 'scorm_package_id' => $scorm->id],
            ['views_count' => 0],
        );

        $stat->increment('views_count');
        $stat->update(['last_viewed_at' => now()]);

        return view('scorm.show', ['url' => $scorm->getUrl()]);
    }
}
