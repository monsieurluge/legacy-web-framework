<?php

namespace App\Services\Request\CustomRequest\Api\Delete;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class Note
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

    public function noteId(): int
    {
        return intval($this->request->pathParameter('id'));
    }
}
