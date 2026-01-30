<?php

declare(strict_types=1);

namespace Essentials\Contract;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

interface RepositoryInterface
{
    public function all(): mixed;

    /** @param mixed $id */
    public function findById($id): mixed;

    /**
     * Finds all records that match the given criteria with the given params
     *
     * @param array<array-key, mixed>|Closure|string|Expression<float|int|string> $column
     * @param mixed $value
     * @param string|Expression<float|int|string>|null $operator
     * @param string $boolean
     */
    public function findWhere(
        array|Closure|string|Expression $column,
        mixed $value = null,
        string|Expression|null $operator = null,
        string $boolean = 'and'
    ): mixed;

    /**
     * @param  mixed  $column
     * @param  mixed  $value
     */
    public function findBy(mixed $column, $value): mixed;
}
