<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait ApiResponse
{
    protected function success($data = null, string $message = 'Operation successful', int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error(
        string $message = 'An error occurred',
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        $errors = null
    ): JsonResponse {
        $response = [
            'status' => false,
            'message' => $message,
            'data' => null,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function paginate($paginateData, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $paginateData->items(),
            'meta' => [
                'current_page' => $paginateData->currentPage(),
                'per_page' => $paginateData->perPage(),
                'total' => $paginateData->total(),
                'last_page' => $paginateData->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    protected function exceptionResponse(Throwable $e, string $defaultMessage = 'An error occurred.'): JsonResponse
    {
        $statusCode = method_exists($e, 'getStatusCode')
            ? $e->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        if (! is_int($statusCode) || $statusCode < 100 || $statusCode >= 600) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $message = config('app.debug')
            ? $e->getMessage()
            : $defaultMessage;

        Log::error('Exception occurred', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null,
        ], $statusCode);
    }
}
