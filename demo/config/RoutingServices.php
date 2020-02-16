<?php

namespace monsieurluge\lwfdemo\Config;

use monsieurluge\lwf\Routing\AlwaysHandleRoute;
use monsieurluge\lwf\Routing\BasicRouter;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\ServiceProvider;
use monsieurluge\lwf\Service\Services;

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
                $router = new BasicRouter();

                $router->register(new AlwaysHandleRoute());

                return $router;
            }
        ];
    }
}
