<?php

require '../init.php';

use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwf\Service\PopulatedServiceProvider;
use monsieurluge\lwfdemo\App\Service\Routing as DemoRoutingService;

// create the service provider

$serviceProvider = new PopulatedServiceProvider(
    new BasicServiceProvider(),
    [
        new DemoRoutingService(),
    ]
);

// run the application

$serviceProvider->provide(Router::class)->dispatch('FIXME: inject request');
