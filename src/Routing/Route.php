<?php

namespace monsieurluge\lwf\Routing;

interface Route
{
    /**
     * Tells if the route can handle this request.
     *
     * @param mixed $request
     *
     * @return bool
     */
    public function canHandle($request): bool;

    /**
     * Handles the request.
     *
     * @param mixed $request
     */
    public function handle($request): void;
}
