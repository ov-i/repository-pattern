<?php

declare(strict_types=1);

namespace Essentials\Repository;

use Closure;
use Essentials\Contract\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use LogicException;

/**
 * @template TModel of Model
 */
abstract class EloquentRepository implements RepositoryInterface
{
    /**
     * Gets the current model fully qualified name.
     *
     * @return class-string<TModel>
     */
    abstract protected static function getModelFQCN(): string;

    final public function model(): Model
    {
        $className = static::getModelFqcn();

        /** @phpstan-ignore-next-line  */
        if (! class_exists($className) || ! is_a($className, Model::class, true)) {
            throw new LogicException("Class $className does not exist or does not extends Illuminate\Database\Eloquent\Model class");
        }

        return new $className();
    }

    /** @return Builder<TModel> */
    public function getQuery(): Builder
    {
        /** @var Builder<TModel> $query */
        $query = $this->model()->newQuery();

        return $query;
    }

    /**
     * Gets all records from the given model's table
     *
     * @return Collection<int, TModel>
     */
    public function all(): Collection
    {
        /** @var Collection<int, TModel> $all */
        $all = $this->getQuery()->get();

        return $all;
    }

    /**
     * finds single record with given id and returns a model or null.
     *
     * @param  int|string  $id
     * @param  string[]  $columns
     * @return TModel|null
     */
    public function findById($id, array $columns = ['*']): ?Model
    {
        return $this->getQuery()->find($id, $columns);
    }

    /**
     * Finds all records that match the given criteria with the given params
     *
     * @param array<array-key, mixed>|Closure|string|Expression<float|int|string> $column
     * @param mixed $value
     * @param string|Expression<float|int|string>|null $operator
     * @param string $boolean
     * @return Builder<TModel>
     */
    public function findWhere(
        array|Closure|string|Expression $column,
        mixed $value = null,
        string|Expression|null $operator = null,
        string $boolean = 'and'
    ): Builder {
        $query = $this->getQuery();

        if (is_array($column) || $column instanceof Closure) {
            return $query->where($column);
        }

        if ($operator === null) {
            return $query->where($column, $value);
        }

        return $query->where($column, $operator, $value, $boolean);
    }


    /**
     * Returns where closure to make sure that column values are from now
     *
     * @param  array<array-key, mixed>|string  $columns
     * @return Builder<TModel>
     */
    public function findFromPast(array|string $columns): Builder
    {
        /** @var Builder<TModel> $past */
        $past = $this->getQuery()->wherePast($columns);

        return $past;
    }

    /**
     * Finds a single record that matches the given column and value as condition.
     * Returns model if the record has been found, null otherwise.
     *
     * @param mixed $column
     * @param array<array-key, mixed>|int|string|bool $value condition that should be used
     * @return TModel|null
     */
    public function findBy(mixed $column, $value): ?Model
    {
        return $this->getQuery()->firstWhere($column, $value);
    }

    /**
     * Gets model's table name
     */
    public function getTable(): string
    {
        return $this->model()->getTable();
    }
}
