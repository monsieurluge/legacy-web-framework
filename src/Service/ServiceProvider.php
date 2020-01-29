<?php

namespace monsieurluge\lwf\Service;

use Closure;
use Exception;

interface ServiceProvider
{
    /**
     * Register a service factory.
     *
     * @param string  $name    the service name
     * @param Closure $factory the factory function as follows: ServiceProvider -> <Service>
     */
    public function register(string $name, Closure $factory): void;

    /**
     * Returns the corresponding service.
     *
     * @param string $name
     *
     * @return mixed
     * @throws Exception if the service does not exist
     */
    public function named(string $name);
}
