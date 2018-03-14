<?php

use Illuminate\Support\Facades\Route;

Route::put(
    '/permission',
    Railroad\Permissions\Controllers\PermissionJsonController::class . '@store'
)->name('permission.store');

Route::patch(
    '/permission/{permissionId}',
    Railroad\Permissions\Controllers\PermissionJsonController::class . '@update'
)->name('permission.update');

Route::delete(
    '/permission/{permissionId}',
    Railroad\Permissions\Controllers\PermissionJsonController::class . '@delete'
)->name('permission.delete');