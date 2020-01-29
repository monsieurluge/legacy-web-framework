<?php

namespace monsieurluge\lwf\Routing;

use Closure;
use Exception;
use monsieurluge\lwf\Routing\Route;
use monsieurluge\lwf\Routing\Router;

final class BasicRouter implements Router
{
    /** @var Route[] */
    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * @inheritDoc
     */
    public function dispatch($request): void
    {
        $dispatched = array_reduce(
            $this->routes,
            $this->dispatchIfPossible($request),
            false
        );

        if (false === $dispatched) {
            throw new Exception('the request has not been handled', $request);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(Route $route): void
    {
        $this->routes[] = $route;
    }

    private function dispatchIfPossible($request): Closure
    {
        return function (bool $dispatched, Route $route) use ($request): bool {
            if (true === $dispatched) {
                return true;
            }

            if (false === $route->canHandle($request)) {
                return false;
            }

            $route->handle($request);

            return true;
        };
    }
}
