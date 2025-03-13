<?php

namespace App\Services;

use App\Enums\PermissionTypeEnum;
use App\Exceptions\BusinessException;
use App\Models\Permission;

class PermissionService
{
    public function tree()
    {
        return Permission::query()->orderBy('order')->get()->toTree();
    }

    public function storeOrUpdate(array $params, ?Permission $permission = null)
    {
        $permission = $this->handleData($permission ?: new Permission, $params);
        $permission->save();

        return $permission;
    }

    protected function handleData(Permission $permission, array $params)
    {
        $typeEnum = PermissionTypeEnum::from($params['type']);

        $permission->parent_id = $params['parent_id'];
        $permission->show_name = $params['show_name'];
        $permission->type = $typeEnum;
        $permission->order = $params['order'];
        $permission->state = true;

        if ($typeEnum === PermissionTypeEnum::DIRECTORY || $typeEnum === PermissionTypeEnum::MENU) {
            $permission->icon = $params['icon'];
            $permission->path = $params['path'];
            $permission->visible = $params['visible'];
            $permission->name = null;
            if ($typeEnum === PermissionTypeEnum::MENU) {
                $permission->component = $params['component'];
                $permission->query = $params['query'];
            }
        }
        if ($typeEnum === PermissionTypeEnum::BUTTON) {
            $permission->name = $params['name'];
        }

        return $permission;
    }

    public function delete(Permission $permission)
    {
        if ($permission->descendants()->count() > 0) {
            throw new BusinessException('该权限下面包含子权限，请处理后再重试');
        }

        $permission->delete();
    }
}
