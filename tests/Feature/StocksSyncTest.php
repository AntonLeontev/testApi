<?php

namespace Tests\Feature;

use App\Http\Controllers\StockController;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Database\Factories\WarehouseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StocksSyncTest extends TestCase
{
	use RefreshDatabase;

	private Warehouse $warehouse;

	public function setUp(): void
	{
		parent::setUp();

		$this->warehouse = WarehouseFactory::new()->create();

		Sanctum::actingAs(User::factory()->create());
	}

    public function test_positive_json_response(): void
    {
		$data = [
			[
				"uuid" => Str::uuid(),
				"stocks" => [
					[
						"uuid" => Str::uuid(),
						"quantity" => 5
					],
					[
						"uuid" => Str::uuid(),
						"quantity" => 6
					]
				]
			]
		];


        $response = $this->postJson(
			action([StockController::class, 'sync']),
			$data
		);


        $response->assertStatus(200);
		$response->assertJson(['success' => true]);
    }

	public function test_adding_stocks_to_database(): void
    {
		$firstId = Str::uuid();
		$secondId = Str::uuid();

		$data = [
			[
				"uuid" => $this->warehouse->id,
				"stocks" => [
					[
						"uuid" => $firstId,
						"quantity" => 5
					],
					[
						"uuid" => $secondId,
						"quantity" => 6
					]
				]
			],
		];


        $response = $this->postJson(
			action([StockController::class, 'sync']),
			$data
		);


        $this->assertDatabaseCount('stocks', 2);
		$this->assertDatabaseHas('stocks', ['warehouse_id' => $this->warehouse->id, 'id' => $firstId, 'quantity' => 5]);
		$this->assertDatabaseHas('stocks', ['warehouse_id' => $this->warehouse->id, 'id' => $secondId,'quantity' => 6]);
    }

	public function test_updating_stocks_in_database(): void
    {
		$id = Str::uuid();
		Stock::create(['warehouse_id' => $this->warehouse->id, 'id' => $id, 'quantity' => 5]);

		$data = [
			[
				"uuid" => $this->warehouse->id,
				"stocks" => [
					[
						"uuid" => $id,
						"quantity" => 25
					],
				]
			],
		];


        $response = $this->postJson(
			action([StockController::class, 'sync']),
			$data
		);


        $this->assertDatabaseCount('stocks', 1);
		$this->assertDatabaseHas('stocks', ['warehouse_id' => $this->warehouse->id, 'id' => $id, 'quantity' => 25]);
    }

	public function test_ignoring_unknown_warehouses(): void
    {
		$data = [
			[
				"uuid" => Str::uuid(),
				"stocks" => [
					[
						"uuid" => Str::uuid(),
						"quantity" => fake()->randomDigit(),
					],
				]
			],
		];


        $response = $this->postJson(
			action([StockController::class, 'sync']),
			$data
		);


        $this->assertDatabaseEmpty('stocks');
    }

	public function test_json_response_with_validation_error(): void
    {
		$data = [
			[
				"stocks" => [
					[
						"uuid" => Str::uuid(),
						"quantity" => fake()->randomDigit(),
					],
				]
			],
		];


        $response = $this->postJson(
			action([StockController::class, 'sync']),
			$data
		);

		$response->assertJson(['success' => false]);
		$response->assertJsonStructure(['success', 'errors']);
    }

	public function test_json_response_with_method_error(): void
    {
		$data = [];


        $response = $this->getJson(
			action([StockController::class, 'sync']),
			$data
		);

		$response->assertJson(['success' => false]);
		$response->assertJsonStructure(['success', 'errors']);
    }
}
