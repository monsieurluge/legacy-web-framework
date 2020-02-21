<?php

namespace App\Services\Routing;

use App\Services\Routing\Router;

interface RoutesGroup
{
    /**
     * Adds the routes to the provided router.
     *
     * @param Router $router
     */
    public function addTo(Router $router): void;
}
