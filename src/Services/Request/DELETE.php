<?php

namespace App\Services\Request;

use App\ServiceInterfaces\Requests\MethodInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTTP DELETE Method
 */
final class DELETE implements MethodInterface
{

    /**
     * @inheritDoc
     */
    public function matches(Request $request): bool
    {
        return 'DELETE' === strtoupper($request->getMethod());
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return 'DELETE';
    }
}
