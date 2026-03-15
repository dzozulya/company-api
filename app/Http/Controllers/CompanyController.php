<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $service
    ) {}

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
