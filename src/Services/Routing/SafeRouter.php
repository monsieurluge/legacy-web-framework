<?php

namespace App\Services\Routing;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Core\Exceptions\NotFoundException;
use App\Services\Request\NoPathParameters;
use App\Services\Request\QueryString;
use App\Services\Routing\Route;
use App\Services\Routing\Router;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Safe Router (Router decorator).
 * Calls a controller (usually a #404 page) when no route can handle the request.
 */
final class SafeRouter implements Router
{

    /** @var [type] **/
    private $controllerForHTTP404;
    /** @var Router **/
    private $origin;

    /**
     * @codeCoverageIgnore
     * @param Router $origin
     * @param [type] $controllerForHTTP404
     */
    public function __construct(Router $origin, $controllerForHTTP404)
    {
        $this->controllerForHTTP404 = $controllerForHTTP404;
        $this->origin               = $origin;
    }

    /**
     * @inheritDoc
     */
    public function add(Route $route): Router
    {
        $this->origin->add($route);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(Request $request, User $user): Result
    {
        try {
            return $this->origin->dispatch($request, $user);
        } catch (NotFoundException $exception) {
            return new Success(
                $this->controllerForHTTP404->process(
                    new QueryString($request),
                    new NoPathParameters(),
                    $request->getContent(),
                    $user
                )
            );
        }
    }

}
