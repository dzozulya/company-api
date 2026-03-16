<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Versionable;
use App\Models\Concerns\HasVersions;
use Illuminate\Database\Eloquent\Model;

final class Company extends Model implements Versionable
{
    use HasVersions;

    protected $fillable = [
        'name',
        'edrpou',
        'address',
    ];

    /**
     * @return array<int, string>
     */
    public function getVersionedFields(): array
    {
        return [
            'name',
            'edrpou',
            'address',
        ];
    }
}
