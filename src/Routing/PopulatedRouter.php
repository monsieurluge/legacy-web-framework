<?php

namespace monsieurluge\lwf\Routing;

use monsieurluge\lwf\Routing\Routes;
use monsieurluge\lwf\Routing\Router;

/**
 * Router decorator.
 * Facilitates the routes registering process.
 */
final class PopulatedRouter implements Router
{
    /** @var bool */
    private $booted;
    /** @var Router */
    private $origin;
    /** @var Routes */
    private $routes;

    public function __construct(Router $origin, Routes $routes)
    {
        $this->booted = false;
        $this->origin = $origin;
        $this->routes = $routes;
    }

    /**
     * @inheritDoc
     */
    public function dispatch($request): void
    {
        if (false === $this->booted) {
            $this->boot();
        }

        $this->origin->dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function register(Route $route): void
    {
        $this->origin->register($route);
    }

    /**
     * Declares all the routes to the decorated router.
     */
    private function boot(): void
    {
        if (true === $this->booted) {
            return;
        }

        $this->routes->declareTo($this->origin);
    }
}
