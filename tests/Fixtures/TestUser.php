<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property string $name
 * @property string $email
 * @property string|null $deleted_at
 */
final class TestUser extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    /** @var array<int, string> */
    protected $guarded = [];

    public $timestamps = false;
}
