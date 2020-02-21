<?php

namespace App\Controller\Pages;

use Symfony\Component\HttpFoundation\Response;

/**
 * PHP Infos Page
 */
final class PhpInfos
{

    /**
     * @inheritDoc
     */
    public function process(): Response
    {
        ob_start();

        phpinfo();

        $infos = ob_get_contents();

        ob_end_flush();

        return new Response($infos, 200);
    }

}
