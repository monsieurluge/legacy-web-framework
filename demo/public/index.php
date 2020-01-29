<?php

require '../init.php';

use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwf\Service\PopulatedServiceProvider;
use monsieurluge\lwfdemo\Config\DummyServices;

$serviceProvider = new PopulatedServiceProvider(
    new BasicServiceProvider(),
    [
        new DummyServices(),
    ]
);

$serviceProvider->named('dieded')->run();
