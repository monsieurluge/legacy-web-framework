<?php

namespace monsieurluge\lwfdemo\Config;

use monsieurluge\lwf\Routing\BasicRouter;
use monsieurluge\lwf\Routing\PopulatedRouter;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Services;
use monsieurluge\lwfdemo\Config\Routing\PagesGet;

final class RoutingServices implements Services
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

    private function services(): array
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
