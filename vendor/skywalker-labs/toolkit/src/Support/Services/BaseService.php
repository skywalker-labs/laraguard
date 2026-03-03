<?php

namespace Skywalker\Support\Support\Services;

use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class BaseService
 *
 * @package Skywalker\Support\Support\Services
 */
abstract class BaseService
{
    /**
     * Execute a callback within a database transaction.
     *
     * @param  callable  $callback
     * @param  int       $attempts
     * @return mixed
     *
     * @throws \Throwable
     */
    protected function transaction(callable $callback, int $attempts = 1)
    {
        return DB::transaction($callback, $attempts);
    }

    /**
     * Return a success response format.
     *
     * @param  mixed   $data
     * @param  string  $message
     * @return array
     */
    protected function success($data = [], string $message = 'Operation successful'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    /**
     * Return an error response format.
     *
     * @param  string  $message
     * @param  array   $errors
     * @return array
     */
    protected function error(string $message = 'Operation failed', array $errors = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ];
    }
}
