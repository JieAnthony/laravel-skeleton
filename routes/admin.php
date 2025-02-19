<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);
});
