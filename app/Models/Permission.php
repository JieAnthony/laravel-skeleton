<?php

namespace App\Models;

use App\Enums\PermissionTypeEnum;
use App\Traits\HasDateTimeFormatterTrait;
use EloquentFilter\Filterable;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use Filterable, HasDateTimeFormatterTrait, NodeTrait;

    public $table = 'permissions';

    public $fillable = [
        '_lft',
        '_rgt',
        'parent_id',
        'show_name',
        'name',
        'guard_name',
        'type',
        'state',
        'visible',
        'icon',
        'component',
        'path',
        'query',
        'order',
    ];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? 'administrator';
        parent::__construct($attributes);
    }

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
            'state' => 'boolean',
            'type' => PermissionTypeEnum::class,
        ];
    }
}
