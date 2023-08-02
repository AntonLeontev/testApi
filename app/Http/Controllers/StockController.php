<?php

namespace App\Http\Controllers;

use App\Http\Actions\StocksSyncAction;
use App\Http\Requests\StocksSyncRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function sync(StocksSyncRequest $request, StocksSyncAction $action): JsonResponse
	{
		try {
			$action($request);
		} catch (\Throwable $th) {
			abort(500, $th->getMessage());
		}

		return response()->json(['success' => true]);
	}
}
