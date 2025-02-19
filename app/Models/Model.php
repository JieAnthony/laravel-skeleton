<?php

namespace App\Models;

use App\Traits\HasDateTimeFormatterTrait;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    use Filterable, HasDateTimeFormatterTrait, HasFactory;
}
