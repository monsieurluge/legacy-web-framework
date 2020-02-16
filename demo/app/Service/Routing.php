<?php

namespace monsieurluge\lwfdemo\App\Service;

use monsieurluge\lwf\Routing\BasicRouter;
use monsieurluge\lwf\Routing\PopulatedRouter;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Service;
use monsieurluge\lwfdemo\Config\Routing\PagesGet;

final class Routing implements Service
{
    /**
     * @inheritDoc
     */
    public function declareTo(ServiceProvider $provider): void
    {
        $provider->register(Router::class, function () {
            return new PopulatedRouter(
                new BasicRouter(),
                new PagesGet()
            );
        });
    }
}
