<?php

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_users_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertOk();
    }

    public function test_pharmacist_cannot_access_users_module(): void
    {
        $pharmacist = User::factory()->pharmacist()->create();

        $response = $this->actingAs($pharmacist)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_pharmacist_can_access_operational_modules(): void
    {
        $pharmacist = User::factory()->pharmacist()->create();

        $this->actingAs($pharmacist)->get(route('customers.index'))->assertOk();
        $this->actingAs($pharmacist)->get(route('medications.index'))->assertOk();
        $this->actingAs($pharmacist)->get(route('inventory.index'))->assertOk();
    }
}
