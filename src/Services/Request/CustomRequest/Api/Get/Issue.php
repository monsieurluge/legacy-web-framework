<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class Issue
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
     * Returns the issue's ID.
     *
     * @return int
     */
    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }
}
