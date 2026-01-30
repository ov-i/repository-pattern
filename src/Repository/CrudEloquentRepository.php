<?php

declare(strict_types=1);

namespace Essentials\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * @template TModel of Model
 *
 * @extends EloquentRepository<TModel>
 */
abstract class CrudEloquentRepository extends EloquentRepository
{
    /**
     * @param  array<array-key, mixed>  $data
     * @return TModel
     */
    protected function create(array $data): Model
    {
        return $this->getQuery()->create($data);
    }

    /**
     * @param TModel $model
     * @param array<array-key, mixed> $data
     *
     * @return bool
     */
    protected function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * @param TModel $model
     * @return bool
     */
    protected function updateDirty(Model $model): bool
    {
        foreach ($model->getDirty() as $field => $value) {
            $model->{$field} = $value;

            $model->update();
        }

        return ! $model->isDirty();
    }

    /**
     * @param TModel $model
     * @param bool $softDelete
     * @return bool
     */
    public function delete(Model $model, bool $softDelete = false): bool
    {
        if ($softDelete && $this->modelIsSoftDeletable()) {
            return $model->delete();
        }

        return $model->forceDelete();
    }

    /**
     * Checks if a given model is soft deletable in class level and database.
     */
    public function modelIsSoftDeletable(): bool
    {
        $model = $this->model();

        return in_array(
            SoftDeletes::class,
            class_uses_recursive($model)
        ) && $this->hasColumn('deleted_at');
    }

    /**
     * Checks if current model contains a given column
     */
    protected function hasColumn(string $column): bool
    {
        return Schema::hasColumn($this->model()->getTable(), $column);
    }
}
