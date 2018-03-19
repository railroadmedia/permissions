<?php

use Illuminate\Support\Facades\Route;

Route::put(
    '/access',
    Railroad\Permissions\Controllers\AccessJsonController::class . '@store'
)->name('access.store');

Route::patch(
    '/access/{accessId}',
    Railroad\Permissions\Controllers\AccessJsonController::class . '@update'
)->name('access.update');

Route::delete(
    '/access/{accessId}',
    Railroad\Permissions\Controllers\AccessJsonController::class . '@delete'
)->name('access.delete');

Route::put(
    '/user-access',
    Railroad\Permissions\Controllers\UserAccessJsonController::class . '@assignAccessToUser'
)->name('user.access.assign');

Route::delete(
    '/user-access',
    Railroad\Permissions\Controllers\UserAccessJsonController::class . '@revokeUserAccess'
)->name('user.access.revoke');


Route::put(
    '/access-hierarchy',
    Railroad\Permissions\Controllers\AccessHierarchyJsonController::class . '@saveAccessHierarchy'
)->name('access.hierarchy.store');

Route::delete(
    '/access-hierarchy',
    Railroad\Permissions\Controllers\AccessHierarchyJsonController::class . '@deleteAccessHierarchy'
)->name('access.hierarchy.delete');

