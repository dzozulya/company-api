<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Versionable;
use App\Models\EntityVersion;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class VersioningService
{
    public function snapshot(Model&Versionable $model): array
    {
        return $model->only($model->getVersionedFields());
    }

    public function makeDiff(array $oldSnapshot, array $newSnapshot): array
    {
        $diff = [];

        foreach ($newSnapshot as $field => $newValue) {
            $oldValue = $oldSnapshot[$field] ?? null;

            if ($oldValue !== $newValue) {
                $diff[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $diff;
    }

    public function getNextVersion(Model&Versionable $model): int
    {
        if (! method_exists($model, 'versions')) {
            throw new InvalidArgumentException('Model must use HasVersions trait.');
        }

        $currentMax = $model->versions()->max('version');

        return ((int) $currentMax) + 1;
    }

    public function createVersion(
        Model&Versionable $model,
        string $event,
        ?array $oldSnapshot = null,
        ?array $newSnapshot = null,
        ?int $createdBy = null,
        ?string $comment = null
    ): EntityVersion {
        if (! method_exists($model, 'versions')) {
            throw new InvalidArgumentException('Model must use HasVersions trait.');
        }

        $snapshot = $newSnapshot ?? $this->snapshot($model);
        $diff = $oldSnapshot !== null ? $this->makeDiff($oldSnapshot, $snapshot) : null;

        /** @var EntityVersion $version */
        $version = $model->versions()->create([
            'version' => $this->getNextVersion($model),
            'event' => $event,
            'snapshot' => $snapshot,
            'diff' => $diff === [] ? null : $diff,
            'created_by' => $createdBy,
            'comment' => $comment,
        ]);

        return $version;
    }
}
