<?php

namespace App\Services;

use Closure;
use Exception;
use App\Services\Provider;

final class BaseProvider implements Provider
{
    private $services;

    public function __construct()
    {
        $this->services = [];
    }

    /**
     * @inheritDoc
     */
    public function add(string $name, Closure $factory): Provider
    {
        $this->services[$name] = $factory;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function named(string $name)
    {
        if (isset($this->services[$name])) {
            return ($this->services[$name])($this);
        }

        throw new Exception(sprintf(
            'the service "%s" cannot be provided, it must be defined first',
            $name
        ));
    }
}
