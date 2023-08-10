<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ResponseTrait;
}
