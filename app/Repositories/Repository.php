<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use ReflectionClass;
use ReflectionException;

/**
 * Class Repository
 * @package App\Repositories
 */
abstract class Repository
{
    protected const MODEL = Model::class;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Find model by parameter
     *
     * @param $param
     * @param array $columns
     * @return Model
     */
    public function find($param, $columns = ["*"]): ?Model
    {
        return $this->builder()->find($param, $columns);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->builder()->get();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->builder()->count();
    }

    /**
     * @param  string $field
     *
     * @return SupportCollection|null
     */
    public function pluck(string $field): ?SupportCollection
    {
        return $this->builder()->pluck($field);
    }

    /**
     * Find by field, first item from collection
     *
     * @param $field
     * @param $value
     * @return Model
     */
    public function whereFirst($field, $value): Model
    {
        return $this->builder()->where($field, $value)->firstOrFail();
    }

    /**
     * @param  array $data
     *
     * @return Builder
     */
    public function whereArray(array $data): Builder
    {
        return $this->builder()->where($data);
    }

    /**
     * Update model
     *
     * @param Model $model
     * @param array $data
     * @param bool $withSave
     * @return Model
     */
    public function updateByArray(Model $model, array $data, bool $withSave = true): Model
    {
        $model->fill($data);

        if ($withSave) {
            $model->save();
        }

        return $model;
    }

    /**
     * @param Collection $collection
     * @param array $data
     * @return int
     */
    public function updateCollectionByArray(Collection $collection, array $data): int
    {
        return call_user_func(static::MODEL  . '::whereIn', 'uuid', $collection->modelKeys())->update($data);
    }

    /**
     * Create Model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->builder()->create($data);
    }

    /**
     * Delete Model
     *
     * @param Model $model
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }

    /**
     * Save model
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    /**
     * @param  bool $withoutNamespace
     * @return string
     *
     * @throws ReflectionException
     */
    public function getModelClass(bool $withoutNamespace = false): string
    {
        $reflectionModel = new ReflectionClass(static::MODEL);

        return $withoutNamespace
            ? $reflectionModel->getShortName()
            : $reflectionModel->getName();
    }

    /**
     * @return Builder
     */
    public function builder(): Builder
    {
        if (!$this->builder) {
            $this->builder = call_user_func([static::MODEL, 'query']);
        }

        return $this->builder;
    }
}
