<?php

namespace App\Controller\Pages;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * "500 Internal error" test Page
 */
final class HTTP500
{

    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        throw new Exception('test page HTTP500 : exception message');
    }

}
