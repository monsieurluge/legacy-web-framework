<?php

namespace monsieurluge\lwf\Service;

use monsieurluge\lwf\Service\ServiceProvider;

interface Services
{
    /**
     * Declares the services to the provider.
     *
     * @param ServiceProvider $provider
     */
    public function declareTo(ServiceProvider $provider): void;
}
