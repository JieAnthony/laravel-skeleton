<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleService
{
    public function list(array $params, ?int $limit = null)
    {
        return Role::query()
            ->select(['id', 'show_name', 'name', 'state', 'created_at'])
            ->filter($params)
            ->paginate(getPageLimit($limit));
    }

    public function storeOrUpdate(array $params, ?Role $role = null)
    {
        $role = $this->handleData($role ?: new Role, $params);
        $role->save();

        if ($params['permission_ids']) {
            $role->syncPermissions($params['permission_ids']);
        } else {
            $role->permissions()->detach();
        }

        return $role;
    }

    protected function handleData(Role $role, array $params)
    {
        if ($role->id === 1) {
            throw new BusinessException('超级管理与员角色不允许操作');
        }
        if ($role->name === null) {
            $role->name = Str::random(12);
        }

        $role->show_name = $params['show_name'];
        $role->state = $params['state'];

        return $role;
    }
}
