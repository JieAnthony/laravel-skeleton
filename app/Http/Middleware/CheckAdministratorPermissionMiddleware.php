<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckAdministratorPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next)
    {
        // debug模式直接通过
        if (App::hasDebugModeEnabled()) {
            return $next($request);
        }

        /** @var \App\Models\Administrator $administrator */
        $administrator = $request->user('administrator');

        if ($administrator->id === 1) {
            return $next($request);
        }

        // 判断是否有超级管理员角色
        $hasSuperRole = Cache::remember('administrator_is_super_role:'.$administrator->id, 86400, function () use ($administrator) {
            return DB::table('model_has_roles')
                ->where('model_type', $administrator::class)
                ->where('model_id', $administrator->id)
                ->where('role_id', 1)
                ->exists();
        });

        if ($hasSuperRole) {
            return $next($request);
        }

        // 开始查询当前管理员是否对该路由有权限
        if ($administrator->can($request->route()->getName())) {
            return $next($request);
        }

        throw new HttpException(403, '暂无权限');
    }
}
