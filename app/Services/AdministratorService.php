<?php

namespace App\Services;

use App\Enums\PermissionTypeEnum;
use App\Exceptions\BusinessException;
use App\Models\Administrator;
use App\Models\Permission;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class AdministratorService
{
    public function login(string $phone, string $password)
    {
        $administrator = Administrator::query()
            ->where('phone', $phone)
            ->first();
        if (! $administrator) {
            throw new BusinessException('账号或者密码错误');
        }
        if (! Hash::check($password, $administrator->password)) {
            throw new BusinessException('账号或者密码错误');
        }

        if (! $administrator->state) {
            throw new BusinessException('账号已被禁用');
        }

        $administrator->tokens()->delete();
        $token = $administrator->createToken('administrator', expiresAt: now()->addDays())->plainTextToken;

        Event::dispatch(new Login('administrator', $administrator, false));

        return $token;
    }

    public function logout(Administrator $administrator)
    {
        $administrator->tokens()->delete();

        Event::dispatch(new Logout('administrator', $administrator));
    }

    public function ownedMenu(Administrator $administrator)
    {
        return Cache::tags('administrator_menu_group')
            ->remember('administrator_menus:'.$administrator->id, 86400, function () use ($administrator) {
                $select = ['id', '_lft', '_rgt', 'parent_id', 'show_name', 'type', 'visible', 'icon', 'component', 'path', 'query', 'order'];

                if (App::hasDebugModeEnabled() || DB::table('model_has_roles')
                    ->where('model_type', $administrator::class)
                    ->where('model_id', $administrator->id)
                    ->where('role_id', 1)
                    ->exists()) {
                    // 全部一起返回
                    return Permission::query()
                        ->select($select)
                        ->whereIn('type', [PermissionTypeEnum::DIRECTORY, PermissionTypeEnum::MENU])
                        ->where('state', true)
                        ->orderBy('order')
                        ->get()
                        ->toTree()
                        ->toArray();
                }

                return $administrator
                    ->getAllPermissions()
                    ->filter(function ($permission) {
                        // 过滤按钮权限
                        return $permission->type !== PermissionTypeEnum::BUTTON && $permission->state;
                    })
                    ->map(function ($permission) use ($select) {
                        // 获取只想显示的字段
                        return $permission->only($select);
                    })
                    ->sortBy('order')
                    ->toTree()
                    ->toArray();
            });
    }

    public function list(array $params, ?int $limit = null)
    {
        return Administrator::query()
            ->with(['roles:id,show_name'])
            ->select(['id', 'username'])
            ->filter($params)
            ->paginate(getPageLimit($limit));
    }

    public function storeOrUpdate(array $params, ?Administrator $administrator = null)
    {
        $administrator = $this->handleData($administrator ?: new Administrator, $params);
        $administrator->save();

        $administrator->syncRoles(! empty($params['role_ids']) ? $params['role_ids'] : []);

        if (! $administrator->state) {
            $this->logout($administrator);
        }

        return $administrator;
    }

    protected function handleData(Administrator $administrator, array $params)
    {
        $administrator->username = $params['username'];
        $administrator->password = Hash::make($params['password']);
        $administrator->state = $params['state'];

        return $administrator;
    }
}
