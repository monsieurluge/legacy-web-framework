<?php

namespace App\Services\Routing;

use Closure;
use monsieurluge\result\Error\Error;
use monsieurluge\result\Error\BaseError;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\ServiceInterfaces\Requests\MethodInterface as Method;
use App\Services\Request\Pattern as Pattern;
use App\Services\Request\Constraint\Constraint;
use App\Services\Request\PathParameters;
use App\Services\Request\QueryString;
use App\Services\Request\Request as AppRequest;
use App\Services\Result\None;
use App\Services\Result\Option;
use App\Services\Result\Some;
use App\Services\Routing\Route;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BaseRoute implements Route
{
    /** @var Constraint[] **/
    private $constraints;
    /** @var Option **/
    private $controllerFactory;
    /** @var Option **/
    private $customRequestFactory;
    /** @var Method **/
    private $method;
    /** @var Pattern **/
    private $pattern;

    /**
     * @param Method  $method
     * @param Pattern $pattern
     */
    public function __construct(Method $method, Pattern $pattern)
    {
        $this->controllerFactory    = new None();
        $this->constraints          = [];
        $this->customRequestFactory = new None();
        $this->method               = $method;
        $this->pattern              = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function canHandle(Request $request): bool
    {
        return
            $this->method->matches($request)
            && $this->pattern->matches($request);
    }

    /**
     * @inheritDoc
     */
    public function expects(array $constraints): Route
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
    * @inheritDoc
    */
    public function for(Closure $controllerFactory): Route
    {
        $this->controllerFactory = new Some($controllerFactory);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, User $user): Result
    {
        $appRequest = new AppRequest(
            new PathParameters($this->pattern->pattern(), $request),
            new QueryString($request),
            $request->getContent(),
            $user
        );

        $errors = array_reduce($this->constraints, $this->gatherErrorsFor($appRequest), []);

        if (0 < count($errors)) {
            return new Failure(
                new BaseError(
                    'sys-1',
                    sprintf('errors : [%s]', implode(', ', $errors))
                )
            );
        }

        $finalRequest = $this->customRequestFactory
            ->map(function($factory) use ($appRequest) { return $factory($appRequest); })
            ->getContentOrDefaultOnFailure($appRequest);

        return new Success(
            $this->controllerFactory
                ->map(function ($factory) use ($finalRequest) { return $factory()->process($finalRequest); })
                ->getContentOrDefaultOnFailure(function () { return new Response('KO'); })
        );
    }

    /**
     * @inheritDoc
     */
    public function using(string $customRequest): Route
    {
        $this->customRequestFactory = new Some(
            function ($request) use ($customRequest) { return new $customRequest($request); }
        );

        return $this;
    }

    /**
     * Returns all the errors that don't match the request's constraints.
     *
     * @param AppRequest $request
     *
     * @return Closure the function as follows: f(errors[], Constraint) -> errors[]
     */
    private function gatherErrorsFor(AppRequest $request): Closure
    {
        return function (array $errors, Constraint $constraint) use ($request) {
            return array_merge(
                $errors,
                $constraint
                    ->validate($request)
                    ->map(function () { return []; }) // return nothing when the constraint is met
                    ->getValueOrExecOnFailure(function (Error $error) { return [ $error->message() ]; })
            );
        };
    }
}
