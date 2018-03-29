<?php

namespace Railroad\Permissions\Middleware;


use Railroad\Permissions\Exceptions\NotAllowedException;
use Railroad\Permissions\Repositories\UserAccessRepository;

class PermissionsMiddleware
{
    protected $accessRepository;

    /**
     * PermissionsMiddleware constructor.
     * @param $accessRepository
     */
    public function __construct(UserAccessRepository $accessRepository)
    {
        $this->accessRepository = $accessRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Get the current route.
        $route = $request->route();

        // Get the current route actions.
        $actions = $route->getAction();

        // Check if a user is logged in.
         if ((!$user = $request->user()) && (!empty($actions['permissions'])) && (empty($request->all())))
        {
             throw new NotAllowedException('This action is unauthorized. Please login');
         }

        $userId = ($request->user()) ? $request->user()->id : null;

        if (!$this->accessRepository->can($userId, $actions, $route->parameterNames())) {
            throw new NotAllowedException('This action is unauthorized.');
        }

        return $next($request);
    }
}