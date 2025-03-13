<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Permission */
class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->whenNotNull($this->parent_id),
            'show_name' => $this->show_name,
            'name' => $this->whenNotNull($this->name),
            'guard_name' => $this->whenNotNull($this->guard_name),
            'type' => $this->whenNotNull($this->type),
            // 'type_text' => $this->whenNotNull($this->type?->description()),
            'state' => $this->whenNotNull($this->state),
            'visible' => $this->whenNotNull($this->visible),
            'icon' => $this->whenNotNull($this->icon),
            'component' => $this->whenNotNull($this->component),
            'path' => $this->whenNotNull($this->path),
            'query' => $this->whenNotNull($this->query),
            'order' => $this->whenNotNull($this->order),
            'created_at' => $this->whenNotNull($this->created_at?->format('Y-m-d H:i')),
            // 'updated_at' => $this->whenNotNull($this->updated_at?->format('Y-m-d H:i')),
        ];
    }
}
