<?php

namespace Tests\Fixtures;

use Essentials\Repository\EloquentRepository;

/**
 * @extends EloquentRepository<"Not\Existing\ClassName">
 * @phpstan-ignore-next-line
 */
final class BadRepository extends EloquentRepository
{
    protected static function getModelFQCN(): string
    {
        /** @phpstan-ignore-next-line */
        return 'Not\\Existing\\ClassName';
    }
}
