<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Administrator */
class AdministratorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            // 'password' => $this->password,
            'name' => $this->name,
            'state' => $this->state,
            'created_at' => $this->created_at?->toDateTimeString(),
            // 'updated_at' => $this->updated_at,
            // 'permissions_count' => $this->permissions_count,
            // 'roles_count' => $this->roles_count,
            // 'tokens_count' => $this->tokens_count,

            'roles' => new RoleCollection($this->whenLoaded('roles')),
        ];
    }
}
