<?php

namespace App\Services\Routing;

use monsieurluge\result\Result\Result;
use App\Services\Routing\Route;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Router Interface
 * @codeCoverageIgnore
 */
interface Router
{

    /**
     * Adds a route to the collection.
     *
     * @param Route $route
     *
     * @return Router
     */
    public function add(Route $route): Router;

    /**
     * Dispatchs the request/user to the route that matches.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return Result a Result<Response>
     */
    public function dispatch(Request $request, User $user): Result;

}
