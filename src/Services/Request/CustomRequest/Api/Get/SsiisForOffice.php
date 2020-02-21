<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class SsiisForOffice
{
    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Returns the office's ID.
     *
     * @return int
     */
    public function officeId(): int
    {
        return intval($this->request->pathParameter('id'));
    }
}
