<?php

namespace App\Services\Routing;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Core\Exceptions\NotAllowedException;
use App\Services\Routing\Route;
use App\Services\Request\NoPathParameters;
use App\Services\Request\QueryString;
use App\Services\Routing\Router;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Security Router (Router decorator).
 * Calls the login page when the user has unsufficient access rights.
 */
final class SecurityRouter implements Router
{

    /** @var [type] **/
    private $loginController;
    /** @var Router **/
    private $origin;
    /** @var [type] **/
    private $unauthorizedController;

    /**
     * @codeCoverageIgnore
     * @param Router $origin
     * @param [type] $loginController
     * @param [type] $unauthorizedController
     */
    public function __construct(
        Router $origin,
        $loginController,
        $unauthorizedController
    ) {
        $this->loginController        = $loginController;
        $this->origin                 = $origin;
        $this->unauthorizedController = $unauthorizedController;
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
        } catch (NotAllowedException $exception) {
            $controller = $user->logged()
                ? $this->unauthorizedController->for($request)
                : $this->loginController;

            return new Success(
                $controller->process(
                    new QueryString(
                        new Request([ 'destination' => $request->getUri() ])
                    ),
                    new NoPathParameters(),
                    $request->getContent(),
                    $user
                )
            );
        }
    }

}
