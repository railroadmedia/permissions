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

Route::put(
    '/assign-permission',
    Railroad\Permissions\Controllers\UserPermissionJsonController::class . '@assignPermissionToUser'
)->name('user.permission.assign');

Route::delete(
    '/user-permission',
    Railroad\Permissions\Controllers\UserPermissionJsonController::class . '@revokeUserPermission'
)->name('user.permission.revoke');

Route::put(
    '/role',
    Railroad\Permissions\Controllers\RoleJsonController::class . '@store'
)->name('role.store');

Route::patch(
    '/role/{roleId}',
    Railroad\Permissions\Controllers\RoleJsonController::class . '@update'
)->name('role.update');

Route::delete(
    '/role/{roleId}',
    Railroad\Permissions\Controllers\RoleJsonController::class . '@delete'
)->name('role.delete');

Route::put(
    '/assign-user-role',
    Railroad\Permissions\Controllers\UserRoleJsonController::class . '@assignRoleToUser'
)->name('user.role.assign');

Route::delete(
    '/user-role',
    Railroad\Permissions\Controllers\UserRoleJsonController::class . '@revokeUserRole'
)->name('user.role.revoke');


