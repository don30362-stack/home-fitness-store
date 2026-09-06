<?php

namespace Tests\Feature;

use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_list_of_cities(): void
    {
        City::query()->create(['name' => '臺北市']);
        City::query()->create(['name' => '臺中市']);

        $response = $this->getJson('/api/cities');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', '臺北市')
            ->assertJsonPath('data.1.name', '臺中市')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ]);
    }

    public function test_it_returns_districts_for_the_selected_city(): void
    {
        $taichung = City::query()->create(['name' => '臺中市']);
        $taipei = City::query()->create(['name' => '臺北市']);

        $taichung->districts()->create([
            'name' => '西屯區',
            'postal_code' => '407',
        ]);

        $taichung->districts()->create([
            'name' => '西區',
            'postal_code' => '403',
        ]);

        $taipei->districts()->create([
            'name' => '中正區',
            'postal_code' => '100',
        ]);

        $response = $this->getJson(
            "/api/cities/{$taichung->id}/districts"
        );

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', '西區')
            ->assertJsonPath('data.0.postal_code', '403')
            ->assertJsonPath('data.1.name', '西屯區')
            ->assertJsonPath('data.1.postal_code', '407')
            ->assertJsonMissing([
                'name' => '中正區',
                'postal_code' => '100',
            ]);
    }

    public function test_it_returns_not_found_for_a_missing_city(): void
    {
        $this->getJson('/api/cities/999/districts')
            ->assertNotFound();
    }

    public function test_it_returns_not_found_for_a_non_numeric_city_id(): void
    {
        $this->getJson('/api/cities/abc/districts')
            ->assertNotFound();
    }
}
