<?php

namespace App\Services\Response;

use Symfony\Component\HttpFoundation\Response;

interface ResponseFactoryInterface
{

    /**
     * Returns a Response object
     *
     * @return Response
     */
    public function create(): Response;

}
