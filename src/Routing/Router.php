<?php

namespace monsieurluge\lwf\Routing;

use monsieurluge\lwf\Routing\Route;

interface Router
{
    /**
     * Dispatches the request.
     *
     * @param mixed $request
     */
    public function dispatch($request): void;

    /**
     * Registers a route.
     *
     * @param Route $route
     */
    public function register(Route $route): void;
}
