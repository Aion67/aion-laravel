<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_update_and_delete_customer(): void
    {
        $staff = User::factory()->pharmacist()->create();

        $this->actingAs($staff)
            ->post(route('customers.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'date_of_birth' => '1990-01-01',
                'sex' => 'male',
                'phone' => '123456789',
                'email' => 'john@example.com',
                'address' => 'Main Street',
                'medical_history' => 'N/A',
                'allergies' => 'Dust',
                'conditions' => 'Asthma',
            ])
            ->assertRedirect(route('customers.index'));

        $customer = Customer::where('email', 'john@example.com')->firstOrFail();

        $this->actingAs($staff)
            ->put(route('customers.update', $customer), [
                'first_name' => 'Johnny',
                'last_name' => 'Doe',
                'date_of_birth' => '1990-01-01',
                'sex' => 'male',
                'phone' => '999',
                'email' => 'johnny@example.com',
                'address' => 'Second Street',
                'medical_history' => 'Updated',
                'allergies' => 'None',
                'conditions' => 'None',
            ])
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Johnny',
            'email' => 'johnny@example.com',
        ]);

        $this->actingAs($staff)
            ->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_staff_can_search_customers(): void
    {
        $staff = User::factory()->pharmacist()->create();

        Customer::create([
            'first_name' => 'Abel',
            'last_name' => 'Stone',
            'date_of_birth' => '1985-02-01',
            'sex' => 'male',
        ]);

        Customer::create([
            'first_name' => 'Mary',
            'last_name' => 'Lane',
            'date_of_birth' => '1988-03-01',
            'sex' => 'female',
        ]);

        $response = $this->actingAs($staff)->get(route('customers.index', ['search' => 'Abel']));

        $response->assertOk();
        $response->assertSee('Abel');
        $response->assertDontSee('Mary');
    }
}
