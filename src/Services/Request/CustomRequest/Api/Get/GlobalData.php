<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class GlobalData
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
     * Returns the user.
     *
     * @return User
     */
    public function user(): User
    {
        return $this->request->user();
    }
}
