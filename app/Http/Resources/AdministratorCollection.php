<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Administrator */
class AdministratorCollection extends ResourceCollection
{
    public $collection = AdministratorResource::class;
}
