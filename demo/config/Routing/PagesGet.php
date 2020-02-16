<?php

namespace monsieurluge\lwfdemo\Config\Routing;

use monsieurluge\lwf\Routing\AlwaysHandleRoute;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Routing\Routes;

final class PagesGet implements Routes
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

    private function routes(): array
    {
        return [
            new AlwaysHandleRoute()
        ];
    }
}
