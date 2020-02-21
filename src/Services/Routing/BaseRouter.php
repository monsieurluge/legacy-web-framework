<?php

namespace App\Services\Routing;

use monsieurluge\result\Result\Result;
use App\Core\Exceptions\NotFoundException;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Routing\Route;
use App\Services\Routing\Router;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Router
 */
final class BaseRouter implements Router
{

    /** @var LoggerInterface **/
    private $logger;
    /** @var Route[] **/
    private $routes;

    /**
     * @codeCoverageIgnore
     * @param LoggerInterface  $logger
     * @param Route[]          $routes
     */
    public function __construct(LoggerInterface $logger, array $routes = [])
    {
        $this->logger = $logger;
        $this->routes = $routes;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function add(Route $route): Router
    {
        $this->routes[] = $route;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Request $request, User $user): Result
    {
        $this->logger->log(
            LogLevel::ERROR,
            sprintf(
                'call [%s] on %s with "%s"',
                $request->getMethod(),
                $request->getPathInfo(),
                $request->getQueryString()
            )
        );

        return $this->routeThatMatches($request)->handle($request, $user);
    }

    /**
     * Returns the Route that matches the given Request
     * @codeCoverageIgnore
     * @param Request $request
     * @return Route
     * @throws NotFoundException if no route was found
     */
    private function routeThatMatches(Request $request): Route
    {
        foreach($this->routes as $route) {
            if (true === $route->canHandle($request)) {
                return $route;
            }
        }

        throw new NotFoundException(sprintf(
            'there is no route to handle this request: %s',
            print_r($request, true)
        ));
    }

}
