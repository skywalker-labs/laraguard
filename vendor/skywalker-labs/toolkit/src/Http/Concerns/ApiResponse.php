<?php

namespace Skywalker\Support\Http\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiSuccess($data, ?string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiError(string $message, int $code = Response::HTTP_BAD_REQUEST, $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    /**
     * Return a no content JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiNoContent(): JsonResponse
    {
        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Return a created JSON response.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiCreated($data, ?string $message = null): JsonResponse
    {
        return $this->apiSuccess($data, $message, Response::HTTP_CREATED);
    }
}
