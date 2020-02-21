<?php

namespace monsieurluge\lwf\Service;

use Closure;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Services;

abstract class AbstractServices implements Services
{
    /**
     * @inheritDoc
     */
    public function addTo(ServiceProvider $provider): void
    {
        foreach ($this->services() as $name => $factory) {
            $provider->register($name, $factory);
        }
    }

    /**
     * Returns the services.
     *
     * @return array<string,Closure>
     */
    abstract protected function services(): array;
}
