<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;

class BusinessException extends \Exception
{
    use ResponseTrait;

    public function render()
    {
        return $this->response()->fail($this->getMessage());
    }
}
