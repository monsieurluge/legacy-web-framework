<?php

namespace App\Services\Request;

use App\ServiceInterfaces\Requests\MethodInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTTP POST Method
 */
final class POST implements MethodInterface
{

    /**
     * @inheritDoc
     */
    public function matches(Request $request): bool
    {
        return 'POST' === strtoupper($request->getMethod());
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return 'POST';
    }
}
