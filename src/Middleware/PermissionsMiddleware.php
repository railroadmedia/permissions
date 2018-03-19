<?php

namespace Railroad\Permissions\Middleware;


use Railroad\Permissions\Repositories\AccessRepository;

class PermissionsMiddleware
{
    protected $accessRepository;

    /**
     * PermissionsMiddleware constructor.
     * @param $accessRepository
     */
    public function __construct(AccessRepository $accessRepository)
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
        // Check if a user is logged in.
         if (!$user = $request->user())
         {
             return $next($request);
         }

        // Get the current route.
        $route = $request->route();

        // Get the current route actions.
        $actions = $route->getAction();

        if(in_array('isOwner', $actions['permissions'])){
            if($this->accessRepository->isOwner($request->user()->id, $route->parameter('id'), $actions['table'])){
                return $next($request);
            }
            else{
                return abort(404);
            }
        }

        return $next($request);
    }
}