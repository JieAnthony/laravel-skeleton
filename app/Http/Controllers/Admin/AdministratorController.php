<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdministratorRequest;
use App\Http\Resources\AdministratorCollection;
use App\Http\Resources\AdministratorResource;
use App\Models\Administrator;
use App\Services\AdministratorService;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    public function __construct(public AdministratorService $administratorService) {}

    public function index(Request $request)
    {
        return $this->response()->success(
            new AdministratorCollection($this->administratorService->list($request->all(), $request->integer('limit')))
        );
    }

    public function store(AdministratorRequest $request)
    {
        $this->administratorService->storeOrUpdate($request->validated());

        return $this->response()->success();
    }

    public function show(Administrator $administrator)
    {
        return $this->response()->success(new AdministratorResource($administrator->load(['roles:id,show_name'])));
    }

    public function update(Administrator $administrator, AdministratorRequest $request)
    {
        $this->administratorService->storeOrUpdate($request->validated(), $administrator);

        return $this->response()->success();
    }
}
