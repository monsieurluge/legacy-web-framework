<?php

namespace monsieurluge\lwf\Routing;

use monsieurluge\lwf\Routing\Route;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Routing\Routes;

abstract class AbstractRoutes implements Routes
{
    /**
     * @inheritDoc
     */
    public function declareTo(Router $router): void
    {
        $routes = $this->routes();

        foreach ($routes as $route) {
            $router->register($route);
        }
    }

    /**
     * Returns all the routes to register.
     *
     * @return Route[]
     */
    abstract protected function routes(): array;
}
