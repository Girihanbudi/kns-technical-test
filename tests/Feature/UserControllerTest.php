<?php

namespace Tests\Feature;

use App\Mail\AdminNewUserNotification;
use App\Mail\NewUserWelcome;
use App\Models\ApiToken;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_user_and_sends_emails(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'password' => 'secret-password',
            'role' => 'administrator',
            'active' => true,
        ]);
        $headers = $this->authHeaders($admin);

        $response = $this->postJson('/api/users', [
            'email' => 'new@example.com',
            'password' => 'new-password',
            'name' => 'New User',
        ], $headers);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 'USER_CREATED',
            ])
            ->assertJsonPath('data.email', 'new@example.com')
            ->assertJsonPath('data.name', 'New User');

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'role' => 'user',
            'active' => 1,
        ]);

        Mail::assertSent(NewUserWelcome::class);
        Mail::assertSent(AdminNewUserNotification::class);
    }

    public function test_index_returns_users_with_permissions_and_filters(): void
    {
        $admin = User::factory()->create([
            'password' => 'secret-password',
            'role' => 'administrator',
            'active' => true,
            'name' => 'Admin',
        ]);
        $headers = $this->authHeaders($admin);

        $user = User::factory()->create([
            'name' => 'Alpha User',
            'email' => 'alpha@example.com',
            'role' => 'user',
            'active' => true,
        ]);
        $manager = User::factory()->create([
            'name' => 'Beta Manager',
            'email' => 'beta@example.com',
            'role' => 'manager',
            'active' => true,
        ]);
        $inactive = User::factory()->create([
            'name' => 'Gamma Inactive',
            'email' => 'gamma@example.com',
            'role' => 'user',
            'active' => false,
        ]);

        Order::create(['user_id' => $user->id]);
        Order::create(['user_id' => $user->id]);
        Order::create(['user_id' => $manager->id]);

        $response = $this->getJson('/api/users?search=alpha&sortBy=name&sortDirection=asc&limit=5&page=1', $headers);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'code' => 'USERS_LIST',
            ]);

        $users = $response->json('data.users');
        $this->assertCount(1, $users, 'Only active users matching search are returned');

        $first = $users[0];
        $this->assertSame($user->email, $first['email']);
        $this->assertSame(2, $first['orders_count']);
        $this->assertTrue($first['can_edit']); // admin can edit anyone
    }

    private function authHeaders(User $user): array
    {
        $plainToken = 'test-token-'.$user->id;

        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'test',
            'token' => hash('sha256', $plainToken),
        ]);

        return [
            'Authorization' => 'Bearer '.$plainToken,
        ];
    }
}
