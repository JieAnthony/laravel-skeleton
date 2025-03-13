<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;

class Administrator extends Authenticatable
{
    use HasRoles;

    protected $table = 'administrators';

    protected $fillable = [
        'name',
        'phone',
        'work_user_id',
        'state',
        'position',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'boolean',
        ];
    }
}
