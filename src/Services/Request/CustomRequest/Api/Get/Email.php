<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class Email
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
     * Returns the e-mail's ID.
     *
     * @return int
     */
    public function emailId(): int
    {
        return intval($this->request->pathParameter('id'));
    }
}
