<?php

namespace App\Domain\Scorm\Policies;

use App\Domain\Scorm\Models\ScormPackage;
use App\Models\User;

class ScormPackagePolicy
{
    public function view(User $user, ScormPackage $scorm): bool
    {
        return true;
    }

    public function delete(User $user, ScormPackage $scorm): bool
    {
        return $user->is_admin;
    }
}
