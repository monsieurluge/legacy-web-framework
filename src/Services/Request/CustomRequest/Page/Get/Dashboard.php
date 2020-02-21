<?php

namespace App\Services\Request\CustomRequest\Page\Get;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class Dashboard
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

    public function user(): User
    {
        return $this->request->user();
    }
}
