<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Tests\TestCase;

use function PHPUnit\Framework\assertSame;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_endpoints(): void
    {
        $this->getJson('/api/me')
            ->assertUnauthorized();

        $this->patchJson('/api/me', [
            'name' => '訪客',
            'email' => 'guest@example.com',
            'phone' => '0912345678',
        ])->assertUnauthorized();

        $this->patchJson('/api/me/password', [
            'current_password' => 'OldPassword123',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_view_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => '測試會員',
            'email' => 'member@example.com',
            'phone' => '0912345678',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user, 'web')
            ->getJson('/api/me');

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', '測試會員')
            ->assertJsonPath('data.email', 'member@example.com')
            ->assertJsonPath('data.phone', '0912345678')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonMissingPath('data.password');
    }

    public function test_authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create([
            'name' => '原本姓名',
            'email' => 'member@example.com',
            'phone' => '0912345678',
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('/api/me', [
                'name' => '修改後姓名',
                'email' => 'member@example.com',
                'phone' => '0987654321',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', '會員資料更新成功')
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', '修改後姓名')
            ->assertJsonPath('data.email', 'member@example.com')
            ->assertJsonPath('data.phone', '0987654321');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '修改後姓名',
            'email' => 'member@example.com',
            'phone' => '0987654321',
        ]);
    }

    public function test_user_cannot_use_another_users_email(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'phone' => '0912345678',
        ]);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
            'phone' => '0987654321',
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('/api/me', [
                'name' => '測試會員',
                'email' => $otherUser->email,
                'phone' => '0912345678',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'member@example.com',
        ]);
    }

    public function test_user_cannot_update_profile_with_invalid_phone(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'phone' => '0912345678',
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('/api/me', [
                'name' => '測試會員',
                'email' => 'member@example.com',
                'phone' => 'abc',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '0912345678'
        ]);
    }

    public function test_user_can_update_their_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('/api/me/password', [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', '密碼更新成功');

        $updatedPassword = $user->fresh()->password;

        $this->assertTrue(
            Hash::check('NewPassword123', $updatedPassword)
        );

        $this->assertFalse(
            Hash::check('OldPassword123', $updatedPassword)
        );
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123',
        ]);

        $originalPassword = $user->password;

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('api/me/password', [
                'current_password' => 'WrongPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'NewPassword123',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['current_password']);

        $this->assertSame(
            $originalPassword,
            $user->fresh()->password
        );
    }

    public function test_user_cannot_update_password_without_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword123'
        ]);

        $originalPassword = $user->password;

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson('/api/me/password', [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword123',
                'password_confirmation' => 'DifferentPassword123',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertSame(
            $originalPassword,
            $user->refresh()->password
        );
    }
}
