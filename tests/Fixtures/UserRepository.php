<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Essentials\Repository\CrudEloquentRepository;
use Illuminate\Database\Eloquent\Model;

/** @extends CrudEloquentRepository<TestUser> */
final class UserRepository extends CrudEloquentRepository
{
    /** @param array<array-key, mixed> $data */
    public function createPublic(array $data): TestUser
    {
        return $this->create($data);
    }

    /**
     * @param TestUser $user
     * @param array<array-key, mixed> $data
     * @return bool
     */
    public function updateUser(TestUser $user, array $data): bool
    {
        return $user->update($data);
    }

    protected static function getModelFQCN(): string
    {
        return TestUser::class;
    }
}
