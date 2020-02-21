<?php

namespace App\ServiceInterfaces\Requests;

use Symfony\Component\HttpFoundation\Request;

/**
 * Pattern Interface
 */
interface PatternInterface
{
    /**
     * Returns the pattern.
     *
     * @return string
     */
    public function pattern(): string;

    /**
     * Tells if the provided request matches the pattern.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function matches(Request $request): bool;
}
