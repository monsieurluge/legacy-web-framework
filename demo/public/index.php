<?php

require '../init.php';

use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwf\Service\PopulatedServiceProvider;
use monsieurluge\lwfdemo\Config\DummyServices;

// create the service provider

$serviceProvider = new PopulatedServiceProvider(
    new BasicServiceProvider(),
    [
        new DummyServices(),
    ]
);

// run the application

$serviceProvider->named('dieded')->run();
