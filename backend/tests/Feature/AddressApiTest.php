<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\District;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_address_endpoints(): void
    {
        $this->getJson('/api/addresses')
            ->assertUnauthorized();

        $this->postJson('api/addresses', [])
            ->assertUnauthorized();

        $this->patchJson('/api/addresses/1', [])
            ->assertUnauthorized();

        $this->deleteJson('/api/addresses/1')
            ->assertUnauthorized();

        $this->patchJson('/api/addresses/1/default')
            ->assertUnauthorized();
    }

    public function test_user_only_sees_their_own_addresses(): void
    {
        $city = City::query()->create([
            'name' => '臺中市',
        ]);

        $district = $city->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407',
        ]);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $user->userAddresses()->create([
            'district_id' => $district->id,
            'label' => '我的住家',
            'recipient_name' => '王小明',
            'recipient_phone' => '0912345678',
            'address' => '臺灣大道三段100號',
            'is_default' => true,
        ]);

        $otherUser->userAddresses()->create([
            'district_id' => $district->id,
            'label' => '別人的地址',
            'recipient_name' => '陳小華',
            'recipient_phone' => '0987654321',
            'address' => '臺灣大道三段200號',
            'is_default' => true,
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->getJson('/api/addresses');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.label', '我的住家')
            ->assertJsonPath('data.0.district.name', '西屯區')
            ->assertJsonPath('data.0.district.postal_code', '407')
            ->assertJsonPath('data.0.district.city.name', '臺中市')
            ->assertJsonMissing([
                'label' => '別人的地址',
            ]);
    }

    public function test_first_address_is_automatically_default(): void
    {
        $city = City::query()->create([
            'name' => '臺中市',
        ]);

        $district = $city->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407',
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'web')
            ->postJson('/api/addresses', [
                'district_id' => $district->id,
                'label' => '住家',
                'recipient_name' => '王小明',
                'recipient_phone' => '0912345678',
                'address' => '臺灣大道三段100號',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', '收件地址新增成功')
            ->assertJsonPath('data.label', '住家')
            ->assertJsonPath('data.is_default', true)
            ->assertJsonPath('data.district.name', '西屯區')
            ->assertJsonPath('data.district.city.name', '臺中市');

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'district_id' => $district->id,
            'label' => '住家',
            'is_default' => true,
        ]);
    }

    public function test_new_default_address_replaces_the_old_default(): void
    {
        $city = City::query()->create([
            'name' => '臺中市'
        ]);

        $district = $city->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407'
        ]);

        $user = User::factory()->create();

        $oldAddress = $user->userAddresses()->create([
            'district_id' => $district->id,
            'label' => '原本住家',
            'recipient_name' => '王小明',
            'recipient_phone' => '0912345678',
            'address' => '臺灣大道三段100號',
            'is_default' => true,
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->postJson('/api/addresses', [
                'district_id' => $district->id,
                'label' => '新住家',
                'recipient_name' => '王小明',
                'recipient_phone' => '0987654321',
                'address' => '臺灣大道三段200號',
                'is_default' => true,
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.label', '新住家')
            ->assertJsonPath('data.is_default', true);

        $this->assertDatabaseHas('user_addresses', [
            'id' => $oldAddress->id,
            'is_default' => false,
        ]);

        $this->assertSame(
            1,
            $user->userAddresses()
                ->where('is_default', true)
                ->count()
        );
    }

    public function test_user_can_partially_update_their_address(): void
    {
        $city = City::query()->create([
            'name' => '臺中市'
        ]);

        $xitun = $city->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407',
        ]);

        $west = $city->districts()->create([
            'name' => '西區',
            'postal_code' => '403',
        ]);

        $user = User::factory()->create();

        $address = $user->userAddresses()->create([
            'district_id' => $xitun->id,
            'label' => '住家',
            'recipient_name' => '王小明',
            'recipient_phone' => '0912345678',
            'address' => '臺灣大道三段100號',
            'is_default' => true,
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson("/api/addresses/{$address->id}", [
                'district_id' => $west->id,
                'label' => '公司',
                'address' => '公益路100號',
            ]);

        $response
            ->assertOk()
            ->assertJsonPath(
                'message',
                '收件地址更新成功'
            )
            ->assertJsonPath('data.label', '公司')
            ->assertJsonPath('data.recipient_name', '王小明')
            ->assertJsonPath('data.district.name', '西區')
            ->assertJsonPath('data.district.postal_code', '403');

        $this->assertDatabaseHas('user_addresses', [
            'id' => $address->id,
            'district_id' => $west->id,
            'label' => '公司',
            'recipient_name' => '王小明',
            'address' => '公益路100號',
        ]);
    }

    public function test_address_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'web')
            ->postJson('/api/addresses', [
                'district_id' => 999,
                'label' => '住家',
                'recipient_name' => '王小明',
                'recipient_phone' => 'abc',
                'address' => '臺灣大道三段100號',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'district_id',
                'recipient_phone'
            ]);

        $this->assertDatabaseCount('user_addresses', 0);
    }

    public function test_user_can_set_an_address_as_default(): void
    {
        $city = City::query()->create([
            'name' => '臺中市'
        ]);

        $district = $city->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407',
        ]);

        $user = User::factory()->create();

        $oldDefault = $user->userAddresses()->create([
            'district_id' => $district->id,
            'label' => '住家',
            'recipient_name' => '王小明',
            'recipient_phone' => '0912345678',
            'address' => '臺灣大道三段100號',
            'is_default' => true,
        ]);

        $newDefault = $user->userAddresses()->create([
            'district_id' => $district->id,
            'label' => '公司',
            'recipient_name' => '王小明',
            'recipient_phone' => '0987654321',
            'address' => '臺灣大道三段200號',
            'is_default' => false,
        ]);

        $response = $this
            ->actingAs($user, 'web')
            ->patchJson("/api/addresses/{$newDefault->id}/default");

        $response
            ->assertOk()
            ->assertJsonPath('message', '預設收件地址設定成功')
            ->assertJsonPath('data.id', $newDefault->id)
            ->assertJsonPath('data.is_default', true);

        $this->assertDatabaseHas('user_addresses', [
            'id' => $oldDefault->id,
            'is_default' => false,
        ]);

        $this->assertDatabaseHas('user_addresses', [
            'id' => $newDefault->id,
            'is_default' => true,
        ]);

        $this->assertSame(
            1,
            $user->userAddresses()
                ->where('is_default', true)
                ->count()
        );
    }
}
