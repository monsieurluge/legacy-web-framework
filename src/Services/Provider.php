<?php

namespace App\Services;

use Closure;

interface Provider
{
    /**
     * Adds a service factory to the collection.
     *
     * @param string  $name
     * @param Closure $factory the factory function as follows: f(Provider) -> Service
     *
     * @return Provider
     */
    public function add(string $name, Closure $factory): Provider;

    /**
     * Returns the corresponding service.
     *
     * @param string $name
     *
     * @throws Exception if the service does not exist
     * @return mixed
     */
    public function named(string $name);
}
