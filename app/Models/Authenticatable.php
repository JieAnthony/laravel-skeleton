<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as LaravelAuthenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

abstract class Authenticatable extends Model implements AuthContract, AuthenticatableContract
{
    use Authorizable, LaravelAuthenticatable;
}
