<?php

use Illuminate\Support\Facades\Route;
Route::group(
    [
        'prefix' => 'permissions',
    ],
    function () {
        Route::put(
            '/user-ability',
            Railroad\Permissions\Controllers\UserAbilityJsonController::class . '@store'
        )->name('permissions.user-ability.store');

        Route::patch(
            '/user-ability/{userAbilityId}',
            Railroad\Permissions\Controllers\UserAbilityJsonController::class . '@update'
        )->name('permissions.user-ability.update');

        Route::delete(
            '/user-ability/{userAbilityId}',
            Railroad\Permissions\Controllers\UserAbilityJsonController::class . '@delete'
        )->name('permissions.user-ability.delete');

        Route::put(
            '/user-role',
            Railroad\Permissions\Controllers\UserRoleJsonController::class . '@store'
        )->name('permissions.user-role.store');

        Route::patch(
            '/user-role/{userRoleId}',
            Railroad\Permissions\Controllers\UserRoleJsonController::class . '@update'
        )->name('permissions.user-role.update');

        Route::delete(
            '/user-role/{userRoleId}',
            Railroad\Permissions\Controllers\UserRoleJsonController::class . '@delete'
        )->name('permissions.user-role.delete');
    });

