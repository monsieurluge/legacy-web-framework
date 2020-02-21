<?php

namespace App\Services;

use App\Services\Provider;

interface ServicesGroup
{
    /**
     * Adds the services to the provider.
     *
     * @param Provider $provider
     */
    public function addTo(Provider $provider): void;
}
