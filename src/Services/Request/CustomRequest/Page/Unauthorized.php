<?php

namespace App\Services\Request\CustomRequest\Page;

use App\Services\Request\Request;

final class Unauthorized
{
    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->request = null;
    }

    /**
     * Returns the destination.
     *
     * @return string
     */
    public function destination(): string
    {
        return is_null($this->request)
            ? 'la requête d\'origine est manquante'
            : $this->request->queryParameter('destination', '"donnée manquante"');
    }

    /**
     * Adds the request reference.
     *
     * @param Request $request
     *
     * @return Unauthorized
     */
    public function for(Request $request): Unauthorized
    {
        $this->request = $request;
    }
}
