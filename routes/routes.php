<?php

use Illuminate\Support\Facades\Route;

Route::put(
    '/permission',
    Railroad\Permissions\Controllers\AbilityJsonController::class . '@storePermission'
)->name('permission.store');

Route::put(
    '/role',
    Railroad\Permissions\Controllers\AbilityJsonController::class . '@storeRole'
)->name('role.store');

Route::patch(
    '/ability/{abilityId}',
    Railroad\Permissions\Controllers\AbilityJsonController::class . '@update'
)->name('ability.update');

Route::delete(
    '/ability/{abilityId}',
    Railroad\Permissions\Controllers\AbilityJsonController::class . '@delete'
)->name('ability.delete');

Route::put(
    '/user-ability',
    Railroad\Permissions\Controllers\UserAbilityJsonController::class . '@assignAbilityToUser'
)->name('user.ability.assign');

Route::delete(
    '/user-ability',
    Railroad\Permissions\Controllers\UserAbilityJsonController::class . '@revokeUserAbility'
)->name('user.ability.revoke');


Route::put(
    '/ability-hierarchy',
    Railroad\Permissions\Controllers\AbilityHierarchyJsonController::class . '@saveAbilityHierarchy'
)->name('ability.hierarchy.store');

Route::delete(
    '/ability-hierarchy',
    Railroad\Permissions\Controllers\AbilityHierarchyJsonController::class . '@deleteAbilityHierarchy'
)->name('ability.hierarchy.delete');

