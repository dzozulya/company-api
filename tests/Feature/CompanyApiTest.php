<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_be_created(): void
    {
        $payload = [
            'name' => 'Test Company',
            'edrpou' => '12345678',
            'address' => 'Kyiv'
        ];

        $response = $this->postJson('/api/company', $payload);

        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'created',
                'version' => 1
            ]);

        $this->assertDatabaseHas('companies', [
            'edrpou' => '12345678'
        ]);

        $this->assertDatabaseHas('company_versions', [
            'version' => 1
        ]);
    }

    public function test_duplicate_request_returns_duplicate_status(): void
    {
        $payload = [
            'name' => 'Test Company',
            'edrpou' => '12345678',
            'address' => 'Kyiv'
        ];

        $this->postJson('/api/company', $payload);

        $response = $this->postJson('/api/company', $payload);

        $response->assertJson([
            'status' => 'duplicate'
        ]);
    }

    public function test_company_update_creates_new_version(): void
    {
        $payload = [
            'name' => 'Test Company',
            'edrpou' => '12345678',
            'address' => 'Kyiv'
        ];

        $this->postJson('/api/company', $payload);

        $payload['address'] = 'Lviv';

        $response = $this->postJson('/api/company', $payload);

        $response->assertJson([
            'status' => 'updated',
            'version' => 2
        ]);

        $this->assertDatabaseHas('company_versions', [
            'version' => 2,
            'address' => 'Lviv'
        ]);
    }

    public function test_company_versions_endpoint(): void
    {
        $payload = [
            'name' => 'Test Company',
            'edrpou' => '12345678',
            'address' => 'Kyiv'
        ];

        $this->postJson('/api/company', $payload);

        $payload['address'] = 'Lviv';

        $this->postJson('/api/company', $payload);

        $response = $this->getJson('/api/company/12345678/versions');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2);
    }
}
