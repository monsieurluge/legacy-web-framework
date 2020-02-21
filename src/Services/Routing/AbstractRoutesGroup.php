<?php

namespace App\Services\Routing;

use App\Services\Routing\Route;
use App\Services\Routing\Router;
use App\Services\Routing\RoutesGroup;

abstract class AbstractRoutesGroup implements RoutesGroup
{
    /**
     * @inheritDoc
     */
    public function addTo(Router $router): void
    {
        foreach ($this->routes() as $route) {
            $router->add($route);
        }
    }

    /**
     * Returns the routes.
     *
     * @return Route[]
     */
    abstract protected function routes(): array;
}
