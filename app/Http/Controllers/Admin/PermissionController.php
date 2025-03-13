<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\PermissionService;

class PermissionController extends Controller
{
    public function __construct(public PermissionService $permissionService) {}

    public function index()
    {
        return $this->response()->success($this->permissionService->tree());
    }

    public function store(PermissionRequest $request)
    {
        $this->permissionService->storeOrUpdate($request->validated());

        return $this->response()->success();
    }

    public function update(Permission $permission, PermissionRequest $request)
    {
        $this->permissionService->storeOrUpdate($request->validated(), $permission);

        return $this->response()->success();
    }

    public function show(Permission $permission)
    {
        return $this->response()->success(new PermissionResource($permission));
    }

    public function destroy(Permission $permission)
    {
        $this->permissionService->delete($permission);

        return $this->response()->success();
    }
}
