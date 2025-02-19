<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->response()->success(message: 'hello world(ADMIN)');
    }
}
