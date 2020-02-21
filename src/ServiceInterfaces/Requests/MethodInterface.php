<?php

namespace App\ServiceInterfaces\Requests;

use Symfony\Component\HttpFoundation\Request;

/**
 * HTTP Method Interface
 * @codeCoverageIgnore
 */
interface MethodInterface
{

    /**
     * Tells if the given method matches the request's HTTP method
     *
     * @param Request $request
     *
     * @return bool
     */
    public function matches(Request $request): bool;

    /**
     * Returns the method as a string
     * @return string
     */
    public function toString(): string;

}
