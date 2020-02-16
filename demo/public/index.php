<?php

require '../init.php';

use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwf\Service\PopulatedServiceProvider;
use monsieurluge\lwfdemo\Config\DemoRoutingServices;

// create the service provider

$serviceProvider = new PopulatedServiceProvider(
    new BasicServiceProvider(),
    [
        new DemoRoutingServices(),
    ]
);

// run the application

$serviceProvider->provide(Router::class)->dispatch('FIXME: inject request');
