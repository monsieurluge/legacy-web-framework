<?php

namespace monsieurluge\lwfdemo\Config;

use monsieurluge\lwf\Routing\BasicRouter;
use monsieurluge\lwf\Routing\PopulatedRouter;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\AbstractServices;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Services;
use monsieurluge\lwfdemo\Config\Routing\PagesGet;

final class DemoRoutingServices extends AbstractServices implements Services
{
    /**
     * @inheritDoc
     */
    protected function services(): array
    {
        return [
            Router::class => function () {
                return new PopulatedRouter(
                    new BasicRouter(),
                    new PagesGet()
                );
            }
        ];
    }
}
