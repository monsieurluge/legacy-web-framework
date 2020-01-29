<?php

namespace monsieurluge\lwf\Service;

use Closure;
use Exception;
use monsieurluge\lwf\Service\ServiceProvider;

final class BasicServiceProvider implements ServiceProvider
{
    /** @var array<string,Closure> */
    private $factories;

    public function __construct()
    {
        $this->factories = [];
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, Closure $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function named(string $name)
    {
        if (isset($this->factories[$name])) {
            return ($this->factories[$name])($this);
        }

        throw new Exception(sprintf(
            'the service "%s" cannot be provided, it must be registered first',
            $name
        ));
    }
}
