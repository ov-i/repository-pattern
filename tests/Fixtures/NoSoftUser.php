<?php

namespace Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class NoSoftUser extends Model
{
    protected $table = 'no_soft_users';
    protected $guarded = [];
    public $timestamps = false;
}
