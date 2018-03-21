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
         if ((!$user = $request->user()) && (!empty($actions['permissions'])))
         {
             throw new NotAllowedException('This action is unauthorized. Please login');
         }

        if(!$this->accessRepository->can($request->user()->id, $actions, $route->parameterNames())){
            throw new NotAllowedException('This action is unauthorized.');
        }

        return $next($request);
    }
}