<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Role */
class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'show_name' => $this->show_name,
            // 'name' => $this->name,
            // 'guard_name' => $this->guard_name,
            'state' => $this->whenNotNull($this->state),
            'created_at' => $this->whenNotNull($this->created_at?->toDateTimeString()),
            // 'updated_at' => $this->updated_at,
            // 'permissions_count' => $this->permissions_count,
            // 'users_count' => $this->users_count,
            'permissions' => new PermissionCollection($this->whenLoaded('permissions')),
        ];
    }
}
