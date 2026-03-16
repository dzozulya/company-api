<?php

declare(strict_types=1);

namespace App\Contracts;

interface Versionable
{
    /**
     * @return array<int, string>
     */
    public function getVersionedFields(): array;
}
