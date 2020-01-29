<?php

namespace monsieurluge\lwf\Routing;

use monsieurluge\lwf\Routing\Route;

final class AlwaysHandleRoute implements Route
{
    /**
     * @inheritDoc
     */
    public function canHandle($request): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function handle($request): void
    {
        echo 'FIXME: handle the request';
    }
}
