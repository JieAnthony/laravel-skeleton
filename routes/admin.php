<?php

namespace App\Http\Controllers\Admin;

use App\Http\Middleware\CheckAdministratorPermissionMiddleware;
use Illuminate\Support\Facades\Route;

Route::name('admin.')->group(function () {

    Route::get('/', [HomeController::class, 'index']);

    // 登录
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware(['auth:administrator'])->group(function () {

        // 当前登录的管理员信息
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        // 退出登录
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        // 获取当前管理员所拥有的菜单
        Route::get('my-menus', [AuthController::class, 'myMenus'])->name('auth.my-menus');

        // 需要权限的路由
        Route::middleware([CheckAdministratorPermissionMiddleware::class])->group(function () {
            /**
             * 管理员
             */
            Route::apiResource('administrators', AdministratorController::class)->except(['destroy']);

            /**
             * 角色
             */
            Route::apiResource('roles', RoleController::class)->except(['destroy']);

            /**
             * 权限
             */
            Route::apiResource('permissions', PermissionController::class)->except(['show']);
        });
    });
});
