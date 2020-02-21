<?php

namespace App\Services\Request;

use App\ServiceInterfaces\Requests\MethodInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTTP GET Method
 */
final class GET implements MethodInterface
{

    /**
     * @inheritDoc
     */
    public function matches(Request $request): bool
    {
        return 'GET' === strtoupper($request->getMethod());
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return 'GET';
    }

}
