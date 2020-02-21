<?php

namespace monsieurluge\lwf\Service;

use monsieurluge\lwf\Service\ServiceProvider;

interface Services
{
    /**
     * Adds the services to the provider.
     *
     * @param ServiceProvider $provider
     */
    public function addTo(ServiceProvider $provider): void;
}
