<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fixtures\TestUser;
use Tests\Fixtures\UserRepository;

final class EloquentRepositoryTest extends TestCase
{

    #[Test]
    public function itReturnCollectionOfModels(): void
    {
        $repository = new UserRepository();

        $repository->createPublic(['email' => 'a@test.com', 'name' => 'A']);
        $repository->createPublic(['email' => 'b@test.com', 'name' => 'B']);

        $all = $repository->all();

        $this->assertCount(2, $all);
        $this->assertInstanceOf(TestUser::class, $all->first());
    }

    #[Test]
    public function itReturnsModelOrNull(): void
    {
        $repository = new UserRepository();

        $created = $repository->createPublic(['email' => 'c@test.com', 'name' => 'C']);

        $found = $repository->findById($created->getKey());

        $this->assertNotNull($found);
        $this->assertSame('c@test.com', $found->email);

        $notFound = $repository->findById(999999);

        $this->assertNull($notFound);
    }

    #[Test]
    public function theEntityCanBeFoundByFindWhereStatement(): void
    {
        $repository = new UserRepository();

        $repository->createPublic(['email' => 'd@test.com', 'name' => 'D']);
        $repository->createPublic(['email' => 'e@test.com', 'name' => 'E']);

        $query = $repository->findWhere('email', 'd@test.com');

        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertSame('D', $results->first()->name);
    }

    #[Test]
    public function theEntityCanBeUpdated(): void
    {
        $repository = new UserRepository();

        $user = $repository->createPublic(['email' => 'test@test.com', 'name' => 'Test']);
        $currentName = $user->name;

        $updated = $repository->updateUser($user, ['name' => 'Updated']);
        $updatedName = $user->name;

        $this->assertNotSame($currentName, $updatedName);
    }

    #[Test]
    public function whenSoftDeletesItMarksDeletedAtWhenUsingModelDelete(): void
    {
        $repository = new UserRepository();
        $created = $repository->createPublic(['email' => 'f@test.com', 'name' => 'F']);

        $repository->delete($created, softDelete: true);

        /** @var ?TestUser $fresh */
        $fresh = TestUser::withTrashed()->find($created->getKey());

        $this->assertNotNull($fresh);
        $this->assertNotNull($fresh->deleted_at, 'deleted_at should be set after soft delete');
    }

    #[Test]
    public function itCanForceDeleteTheModel(): void
    {
        $repository = new UserRepository();
        $created = $repository->createPublic(['email' => 'g@test.com', 'name' => 'G']);

        $repository->delete($created, softDelete: false);

        $fresh = TestUser::withTrashed()->find($created->getKey());

        $this->assertNull($fresh, 'Row should be removed after force delete');
    }

    #[Test]
    public function findWhereWithTwoArgumentsBuildsExpectedSqlAndBindings(): void
    {
        $repository = new UserRepository();

        $query = $repository->findWhere('email', 'd@test.com');

        $this->assertSame(
            'select * from "users" where "email" = ? and "users"."deleted_at" is null',
            $query->toSql()
        );
        $this->assertSame(['d@test.com'], $query->getBindings());
    }

    #[Test]
    public function findWhereAcceptsArrayConditions(): void
    {
        $repository = new UserRepository();

        $repository->createPublic(['email' => 'x@test.com', 'name' => 'X']);
        $repository->createPublic(['email' => 'y@test.com', 'name' => 'Y']);

        $results = $repository->findWhere(['email' => 'x@test.com'])->get();
xxx
        $this->assertCount(1, $results);
        $this->assertSame('X', $results->first()->name);
    }

    #[Test]
    public function findWhereAcceptsOperator(): void
    {
        $repository = new UserRepository();

        $repository->createPublic(['email' => 'like@test.com', 'name' => 'Like']);
        $repository->createPublic(['email' => 'other@test.com', 'name' => 'Other']);

        $results = $repository->findWhere('email', '%@test.com', 'like')->get();

        $this->assertCount(2, $results);
    }

    #[Test]
    public function findByReturnsSingleModelWhenFound(): void
    {
        $repository = new UserRepository();
        $repository->createPublic(['email' => 'findby@test.com', 'name' => 'FB']);

        $found = $repository->findBy('email', 'findby@test.com');

        $this->assertNotNull($found);
        $this->assertSame('FB', $found->name);
    }

    #[Test]
    public function findByAcceptsOperator(): void
    {
        $repository = new UserRepository();
        $repository->createPublic(['email' => 'aaa@test.com', 'name' => 'A']);
        $repository->createPublic(['email' => 'bbb@test.com', 'name' => 'B']);

        $found = $repository->findBy('email', 'bbb@test.com');

        $this->assertNotNull($found);
        $this->assertSame('B', $found->name);
    }

    #[Test]
    public function deleteWithSoftDeleteTrueShouldNotRemoveRow(): void
    {
        $repository = new UserRepository();
        $created = $repository->createPublic(['email' => 'soft@test.com', 'name' => 'Soft']);

        $repository->delete($created, softDelete: true);

        $user = $repository->findById($created->getKey());

        $this->assertNull($user, 'Soft deleted model should not be visible normally');

        /** @var ?TestUser $withTrashed */
        $withTrashed = TestUser::withTrashed()->find($created->getKey());
        $this->assertNotNull($withTrashed);
        $this->assertNotNull($withTrashed->deleted_at);
    }

    #[Test]
    public function deleteWithSoftDeleteFalseShouldRemoveRowCompletely(): void
    {
        $repository = new UserRepository();
        $created = $repository->createPublic(['email' => 'force@test.com', 'name' => 'Force']);

        $repository->delete($created, softDelete: false);

        /** @var ?TestUser $user */
        $user = TestUser::withTrashed()->find($created->getKey());

        $this->assertNull($user);
    }

    #[Test]
    public function modelIsSoftDeletableReturnsFalseWhenColumnIsMissing(): void
    {
        $repository = new \Tests\Fixtures\NoSoftUserRepository();

        $this->assertFalse($repository->modelIsSoftDeletable());
    }

    #[Test]
    public function modelThrowsWhenModelClassDoesNotExist(): void
    {
        $repository = new \Tests\Fixtures\BadRepository();

        $this->expectException(\LogicException::class);

        $repository->model();
    }

    #[Test]
    public function findRespectsColumnsSelection(): void
    {
        $repository = new UserRepository();
        $created = $repository->createPublic(['email' => 'cols@test.com', 'name' => 'Cols']);

        $found = $repository->findById($created->getKey(), ['id', 'email']);

        $this->assertNotNull($found);
        $this->assertSame('cols@test.com', $found->email);
    }
}
