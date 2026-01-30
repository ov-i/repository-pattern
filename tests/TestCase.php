<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $schema = Capsule::schema();

        if (! $schema->hasTable('users')) {
            $schema->create('users', function ($table): void {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('name');
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (! $schema->hasTable('no_soft_users')) {
            $schema->create('no_soft_users', function ($table): void {
                $table->increments('id');
                $table->string('email')->unique();
                $table->string('name');
            });
        }
    }

    protected function tearDown(): void
    {
        $schema = Capsule::schema();

        if ($schema->hasTable('users')) {
            $schema->drop('users');
        }

        parent::tearDown();
    }
}
