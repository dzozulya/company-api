<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $service
    ) {}

    /**
     * @throws \Throwable
     */
    public function store(CompanyRequest $request)
    {
        return response()->json(
            $this->service->store($request->validated())
        );
    }

    public function versions(string $edrpou)
    {
        return response()->json(
            $this->service->getVersions($edrpou)
        );
    }
}
