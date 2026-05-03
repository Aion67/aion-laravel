<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'name' => 'Alice Staff',
                'email' => 'alice@example.com',
                'role' => User::ROLE_PHARMACIST,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'alice@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'name' => 'Alice Updated',
                'email' => 'alice.updated@example.com',
                'role' => User::ROLE_ADMIN,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Alice Updated',
            'email' => 'alice.updated@example.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_filter_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create(['name' => 'Chief Admin', 'email' => 'chief@example.com']);
        User::factory()->pharmacist()->create(['name' => 'Pharm One', 'email' => 'pharm@example.com']);

        $response = $this->actingAs($admin)->get(route('users.index', [
            'search' => 'chief',
            'role' => User::ROLE_ADMIN,
        ]));

        $response->assertOk();
        $response->assertSee('Chief Admin');
        $response->assertDontSee('Pharm One');
    }
}
