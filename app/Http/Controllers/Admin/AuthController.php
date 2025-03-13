<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthLoginRequest;
use App\Http\Resources\AdministratorResource;
use App\Services\AdministratorService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(public AdministratorService $administratorService) {}

    public function login(AuthLoginRequest $request)
    {
        $token = $this->administratorService->login(
            $request->validated('username'),
            $request->validated('password')
        );

        return $this->response()->success(compact('token'));
    }

    public function me(Request $request)
    {
        return $this->response()->success(new AdministratorResource($request->user('administrator')));
    }

    public function logout(Request $request)
    {
        $this->administratorService->logout($request->user('administrator'));

        return $this->response()->success();
    }

    public function myMenus(Request $request)
    {
        return $this->response()->success($this->administratorService->ownedMenu($request->user('administrator')));
    }
}
