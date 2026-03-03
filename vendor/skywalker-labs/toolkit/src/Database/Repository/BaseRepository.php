<?php

namespace Skywalker\Support\Database\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Container\Container;
use Skywalker\Support\Exceptions\PackageException;

/**
 * Class BaseRepository
 *
 * @package Skywalker\Support\Database\Repository
 */
abstract class BaseRepository implements RepositoryContract
{
    /**
     * The repository model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    /**
     * Resolve the model from the container.
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Skywalker\Support\Exceptions\PackageException
     */
    protected function resolveModel(): Model
    {
        $modelClass = $this->model();

        if (! class_exists($modelClass)) {
            throw new PackageException("Class {$modelClass} does not exist.");
        }

        return Container::getInstance()->make($modelClass);
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * @inheritDoc
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * @inheritDoc
     */
    public function find($id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data): bool
    {
        $item = $this->find($id);

        if ($item) {
            return $item->update($data);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool
    {
        $item = $this->find($id);

        if ($item) {
            return $item->delete();
        }

        return false;
    }
}
