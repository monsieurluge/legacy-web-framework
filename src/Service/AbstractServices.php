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
    public function declareTo(ServiceProvider $provider): void
    {
        $services = $this->services();

        foreach ($services as $name => $factory) {
            $provider->register($name, $factory);
        }
    }

    /**
     * Returns all the services to declare.
     *
     * @return array<string,Closure>
     */
    abstract protected function services(): array;
}
