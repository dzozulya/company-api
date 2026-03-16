<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\EntityVersion;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVersions
{
    public function versions(): MorphMany
    {
        return $this->morphMany(EntityVersion::class, 'versionable');
    }


}
