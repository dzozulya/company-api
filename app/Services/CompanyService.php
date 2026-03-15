<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CompanyService
{
    /**
     * @throws \Throwable
     */
    public function store(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $company = $this->findCompanyForUpdate($data['edrpou']);

            if (!$company) {
                return $this->createCompany($data);
            }

            return $this->updateCompany($company, $data);
        });
    }

    public function getVersions(string $edrpou) : Collection
    {
        $company = Company::where('edrpou', $edrpou)->firstOrFail();

        return $company
            ->versions()
            ->orderBy('version')
            ->get();
    }

    private function findCompanyForUpdate(string $edrpou): ?Company
    {
        return Company::where('edrpou', $edrpou)
            ->lockForUpdate()
            ->first();
    }
    private function createCompany(array $data): array
    {
        $company = Company::create($data);

        $version = $this->createVersion($company);

        return [
            'status' => 'created',
            'company_id' => $company->id,
            'version' => $version
        ];
    }
    private function updateCompany(Company $company, array $data): array
    {
        $changes = $this->diff(
            $company->only(['name','edrpou','address']),
            $data
        );

        if (empty($changes)) {

            return [
                'status' => 'duplicate',
                'company_id' => $company->id,
                'version' => $company->versions()->max('version')
            ];
        }

        $company->update($data);

        $version = $this->createVersion($company);

        return [
            'status' => 'updated',
            'company_id' => $company->id,
            'version' => $version,
            'changes' => $changes
        ];
    }

    private function createVersion(Company $company): int
    {
        $version = ($company->versions()->max('version') ?? 0) + 1;

        CompanyVersion::create([
            'company_id' => $company->id,
            'version' => $version,
            'name' => $company->name,
            'edrpou' => $company->edrpou,
            'address' => $company->address,
            'created_at' => now()
        ]);

        return $version;
    }
    protected function diff(array $old, array $new): array
    {
        $changes = [];

        foreach ($new as $key => $value) {

            if (!array_key_exists($key, $old)) {
                continue;
            }

            if ($old[$key] !== $value) {

                $changes[$key] = [
                    'old' => $old[$key],
                    'new' => $value
                ];
            }
        }

        return $changes;
    }



}
