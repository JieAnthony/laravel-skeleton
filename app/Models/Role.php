<?php

namespace App\Models;

use App\Traits\HasDateTimeFormatterTrait;
use EloquentFilter\Filterable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Filterable, HasDateTimeFormatterTrait;

    public $table = 'roles';

    protected $fillable = [
        'show_name',
        'name',
        'guard_name',
        'state',
    ];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? 'administrator';
        parent::__construct($attributes);
    }
}
