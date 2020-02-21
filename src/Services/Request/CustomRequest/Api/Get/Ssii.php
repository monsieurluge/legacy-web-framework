<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class Ssii
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
     * Returns the SSII's ID.
     *
     * @return string
     */
    public function ssiiId(): string
    {
        return $this->request->pathParameter('id');
    }
}
