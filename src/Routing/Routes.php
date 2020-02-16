<?php

namespace monsieurluge\lwf\Routing;

use monsieurluge\lwf\Routing\Router;

interface Routes
{
    public function declareTo(Router $router): void;
}
