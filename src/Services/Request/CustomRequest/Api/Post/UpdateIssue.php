<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class UpdateIssue
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

    public function body()
    {
        return $this->request->body();
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

    public function user(): User
    {
        return $this->request->user();
    }
}
