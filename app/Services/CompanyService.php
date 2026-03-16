<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class CompanyService
{
    public function __construct(
        private readonly VersioningService $versioningService,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function store(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $company = $this->findCompanyByEdrpouForUpdate($data['edrpou']);

            if ($company === null) {
                return $this->createCompany($data);
            }

            return $this->updateCompanyIfNeeded($company, $data);
        });
    }

    public function getVersions(string $edrpou): Collection
    {
        $company = Company::query()
            ->where('edrpou', $edrpou)
            ->first();

        if ($company === null) {
            throw new ModelNotFoundException('Company not found.');
        }

        return $company->versions()
            ->orderBy('version')
            ->get();
    }

    private function findCompanyByEdrpouForUpdate(string $edrpou): ?Company
    {
        return Company::query()
            ->where('edrpou', $edrpou)
            ->lockForUpdate()
            ->first();
    }

    private function createCompany(array $data): array
    {
        $company = Company::query()->create($this->makeCompanyAttributes($data));

        $this->versioningService->createVersion(
            model: $company,
            event: 'created',
            oldSnapshot: null,
            newSnapshot: $this->versioningService->snapshot($company),
            createdBy: $this->getCurrentUserId(),
           comment: 'Initial company version',
        );

        return $this->makeCreatedResponse($company);
    }

    private function updateCompanyIfNeeded(Company $company, array $data): array
    {
        $oldSnapshot = $this->versioningService->snapshot($company);
        $newSnapshot = $this->makeCompanyAttributes($data);
        $diff = $this->versioningService->makeDiff($oldSnapshot, $newSnapshot);

        if ($diff === []) {
            return $this->makeDuplicateResponse($company);
        }

        $company->update($newSnapshot);

        $this->versioningService->createVersion(
            model: $company,
            event: 'updated',
            oldSnapshot: $oldSnapshot,
            newSnapshot: $newSnapshot,
            createdBy: $this->getCurrentUserId(),
            comment: 'Company updated',
        );

        return $this->makeUpdatedResponse($company->fresh(), $diff);
    }

    private function makeCompanyAttributes(array $data): array
    {
        return [
            'name' => $data['name'],
            'edrpou' => $data['edrpou'],
            'address' => $data['address'],
        ];
    }

    private function makeCreatedResponse(Company $company): array
    {
        return [
            'status' => 'created',
            'message' => 'Company created successfully',
            'data' => $company,
        ];
    }

    private function makeDuplicateResponse(Company $company): array
    {
        return [
            'status' => 'duplicate',
            'message' => 'No changes detected',
            'data' => $company,
        ];
    }

    private function makeUpdatedResponse(Company $company, array $diff): array
    {
        return [
            'status' => 'updated',
            'message' => 'Company updated successfully',
            'data' => $company,
            'changes' => $diff,
        ];
    }


    private function getCurrentUserId(): ?int
    {
        $userId = Auth::id();

        return $userId !== null ? (int) $userId : null;
    }
}
