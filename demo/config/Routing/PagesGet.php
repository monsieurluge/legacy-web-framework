<?php

namespace monsieurluge\lwfdemo\Config\Routing;

use monsieurluge\lwf\Routing\AlwaysHandleRoute;
use monsieurluge\lwf\Routing\AbstractRoutes;
use monsieurluge\lwf\Routing\Router;
use monsieurluge\lwf\Routing\Routes;

final class PagesGet extends AbstractRoutes implements Routes
{
    /**
     * @inheritDoc
     */
    protected function routes(): array
    {
        return [
            new AlwaysHandleRoute()
        ];
    }
}
