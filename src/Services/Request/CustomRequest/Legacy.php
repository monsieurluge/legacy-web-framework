<?php

namespace App\Services\Request\CustomRequest;

use Exception;
use App\Services\Request\Request;
use App\Services\Security\User\User;

final class Legacy
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

    public function method(): string
    {
        if (empty($this->request->queryParameter('f', ''))) {
            throw new Exception('the method name ("f" param) must be provided');
        }

        return $this->request->queryParameter('f', '');
    }

    public function route(): string
    {
        if (empty($this->request->queryParameter('r', ''))) {
            throw new Exception('the route name ("r" param) must be provided');
        }

        return $this->request->queryParameter('r', '');
    }

    public function user(): User
    {
        return $this->request->user();
    }
}
