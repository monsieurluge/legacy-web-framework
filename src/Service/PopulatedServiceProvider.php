<?php

namespace monsieurluge\lwf\Service;

use Closure;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Service;

/**
 * Service provider decorator.
 * Facilitates the services registering process.
 */
final class PopulatedServiceProvider implements ServiceProvider
{
    /** @var bool */
    private $booted;
    /** @var ServiceProvider */
    private $origin;
    /** @var Service[] */
    private $services;

    /**
     * @param ServiceProvider $origin
     * @param Service[]      $services
     */
    public function __construct(ServiceProvider $origin, array $services)
    {
        $this->booted   = false;
        $this->origin   = $origin;
        $this->services = $services;
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, Closure $factory): void
    {
        $this->origin->register($name, $factory);
    }

    /**
     * @inheritDoc
     */
    public function provide(string $name)
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->origin->provide($name);
    }

    /**
     * Declares all the services to the decorated service provider.
     */
    private function boot(): void
    {
        if (true === $this->booted) {
            return;
        }

        foreach ($this->services as $services) {
            $services->declareTo($this->origin);
        }
    }
}
