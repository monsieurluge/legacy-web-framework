<?php

require '../init.php';

use monsieurluge\lwf\Service\BasicServiceProvider;
use monsieurluge\lwfdemo\Config\DummyServices;

$serviceProvider = new BasicServiceProvider();

(new DummyServices())->declareTo($serviceProvider);

$serviceProvider->named('dieded')->run();
