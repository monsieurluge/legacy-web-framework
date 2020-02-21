<?php

namespace App\Services\Routing;

use App\Services\Request\DELETE;
use App\Services\Request\GET;
use App\Services\Request\Pattern;
use App\Services\Request\POST;
use App\Services\Routing\BaseRoute;
use App\Services\Routing\Route;

final class RouteFactory
{
    /**
     * Creates a "DELETE" route.
     *
     * @return Route
     */
    static public function delete(string $pattern, array $constraints = []): Route
    {
        return (new BaseRoute(new DELETE(), new Pattern($pattern)))->expects($constraints);
    }

    /**
     * Creates a "GET" route.
     *
     * @return Route
     */
    static public function get(string $pattern, array $constraints = []): Route
    {
        return (new BaseRoute(new GET(), new Pattern($pattern)))->expects($constraints);
    }

    /**
     * Creates a "POST" route.
     *
     * @return Route
     */
    static public function post(string $pattern, array $constraints = []): Route
    {
        return (new BaseRoute(new POST(), new Pattern($pattern)))->expects($constraints);
    }
}
