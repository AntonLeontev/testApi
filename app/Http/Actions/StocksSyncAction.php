<?php

namespace App\Http\Actions;

use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StocksSyncAction
{
	public function __invoke(Request $request)
	{
		foreach ($request->json() as $warehouse) {
			if (! Warehouse::where('id', $warehouse['uuid'])->exists()) {
				return;
			}

			$this->syncStocks($warehouse);
		}
	}

	private function syncStocks(array $warehouse): void
	{
		foreach ($warehouse['stocks'] as $stock) {
			Stock::updateOrCreate(
				[
					'warehouse_id' => $warehouse['uuid'],
					'id' => $stock['uuid']
				],
				[
					'quantity' => $stock['quantity']
				]
			);
		}
	}
}
