<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class Offices
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
     * Returns the search term.
     *
     * @return string
     */
    public function term(): string
    {
        return $this->request->queryParameter('term', '');
    }
}
