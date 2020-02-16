<?php

namespace monsieurluge\lwf\Service;

use monsieurluge\lwf\Service\ServiceProvider;

interface Service
{
    /**
     * Declares the service to the provider.
     *
     * @param ServiceProvider $provider
     */
    public function declareTo(ServiceProvider $provider): void;
}
