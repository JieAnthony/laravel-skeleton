<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(public RoleService $roleService) {}

    public function index(Request $request)
    {
        return $this->response()->success(
            new RoleCollection(
                $this->roleService->list(
                    $request->all(),
                    $request->integer('limit')
                )
            )
        );
    }

    public function store(RoleRequest $request)
    {
        $this->roleService->storeOrUpdate($request->validated());

        return $this->response()->success();
    }

    public function update(Role $role, RoleRequest $request)
    {
        $this->roleService->storeOrUpdate($request->validated(), $role);

        return $this->response()->success();
    }

    public function show(Role $role)
    {
        return $this->response()->success(new RoleResource($role->load(['permissions:id,show_name'])));
    }
}
