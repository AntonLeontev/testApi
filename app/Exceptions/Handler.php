<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

	public function render($request, Throwable $exception)
	{
		if ($exception instanceof ValidationException) {
			return response()->json([
				'success' => false,
				'errors' => $exception->errors(),
			], $exception->status);
		}


		return response()->json([
			'success' => false,
			'errors' => [$exception->getMessage()],
		], 500);
	}
}
