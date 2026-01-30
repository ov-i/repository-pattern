<?php

namespace Tests\Fixtures;

use Essentials\Repository\CrudEloquentRepository;

/** @extends CrudEloquentRepository<NoSoftUser> */
final class NoSoftUserRepository extends CrudEloquentRepository
{
    protected static function getModelFQCN(): string
    {
        return NoSoftUser::class;
    }
}
