<?php

namespace App\Services;

use App\Services\Provider;
use App\Services\ServicesGroup;

abstract class AbstractServicesGroup implements ServicesGroup
{
    /**
     * @inheritDoc
     */
    public function addTo(Provider $provider): void
    {
        foreach ($this->services() as $name => $factory) {
            $provider->add($name, $factory);
        }
    }

    /**
     * Returns the services.
     *
     * @return array
     */
    abstract protected function services(): array;
}
