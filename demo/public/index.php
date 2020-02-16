<?php

require '../init.php';

use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwf\Service\PopulatedServiceProvider;
use monsieurluge\lwfdemo\Config\RoutingServices;

// create the service provider

$serviceProvider = new PopulatedServiceProvider(
    new BasicServiceProvider(),
    [
        new RoutingServices(),
    ]
);

// run the application

$serviceProvider->provide(Router::class)->dispatch(null);
