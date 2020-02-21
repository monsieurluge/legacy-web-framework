<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class ReadIssue
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

    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }

    public function newState()
    {
        return json_decode($this->request->body(), true)['read'];
    }
}
