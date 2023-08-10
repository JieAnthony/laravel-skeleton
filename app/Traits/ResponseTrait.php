<?php

namespace App\Traits;

use App\Http\Response;

trait ResponseTrait
{
    /**
     * @return Response
     */
    public function response()
    {
        return app(Response::class);
    }
}
