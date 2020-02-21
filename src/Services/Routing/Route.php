<?php

namespace App\Services\Routing;

use Closure;
use monsieurluge\result\Result\Result;
use App\Services\Request\Constraint\Constraint;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Route
{
    /**
     * Tells if the route can handle the request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function canHandle(Request $request): bool;

    /**
     * Expects some constraints.
     *
     * @param Constraint[] $constraints
     *
     * @return Route
     */
    public function expects(array $constraints): Route;

    /**
     * Declare the controller.
     *
     * @param Closure $controllerFactory a closure as follows: f() -> Controller
     *
     * @return Route
     */
    public function for(Closure $controllerFactory): Route;

    /**
     * Handle the provided request and user.
     *
     * @param Request $request
     * @param User    $user
     *
     * @return Result a Result<Response>
     */
    public function handle(Request $request, User $user): Result;

    /**
     * Declare a custom request.
     *
     * @param string $customRequest the custom request's class name
     *
     * @return Route
     */
    public function using(string $customRequest): Route;
}
