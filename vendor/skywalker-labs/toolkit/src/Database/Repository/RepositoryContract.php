<?php

namespace Skywalker\Support\Database\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface RepositoryContract
 *
 * @package Skywalker\Support\Database\Repository
 */
interface RepositoryContract
{
    /**
     * Get all items.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find item by ID.
     *
     * @param  int|string  $id
     * @param  array       $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find($id, array $columns = ['*']): ?Model;

    /**
     * Create a new item.
     *
     * @param  array  $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model;

    /**
     * Update an item.
     *
     * @param  int|string  $id
     * @param  array       $data
     * @return bool
     */
    public function update($id, array $data): bool;

    /**
     * Delete an item.
     *
     * @param  int|string  $id
     * @return bool
     */
    public function delete($id): bool;
}
