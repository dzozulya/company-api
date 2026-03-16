<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class EntityVersion extends Model
{
    protected $table = 'entity_versions';

    protected $fillable = [
        'version',
        'event',
        'snapshot',
        'diff',
        'created_by',
        'comment',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'diff' => 'array',
    ];

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }
}
