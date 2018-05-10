<?php

namespace Railroad\Permissions\Middleware;

use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Repositories\UserAccessRepository;
use Railroad\Permissions\Services\PermissionService;

class PermissionsMiddleware
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * PermissionsMiddleware constructor.
     *
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws NotAllowedException
     */
    public function handle($request, \Closure $next)
    {
        // Get the current route.
        $route = $request->route();

        // Get the current route actions.
        $actions = $route->getAction();

        // If any required roles or abilities as set, there must be a logged in user
        if ((!empty($actions['roles']) || !empty($actions['abilities'])) && empty($request->user())) {
            throw new NotAllowedException('This action is unauthorized. Please login');
        }

        if (!empty($request->user())) {
            // make sure the user has all the required roles
            foreach ($actions['roles'] ?? [] as $role) {
                if (!$this->permissionService->is($request->user()->id, $role)) {
                    throw new NotAllowedException('This action is unauthorized.');
                }
            }

            // make sure the user has all the required abilities
            foreach ($actions['abilities'] ?? [] as $ability) {
                if (!$this->permissionService->can($request->user()->id, $ability)) {
                    throw new NotAllowedException('This action is unauthorized.');
                }
            }
        }

        return $next($request);
    }
}