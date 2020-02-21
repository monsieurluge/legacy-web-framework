<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class CreateIssue
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
     * Returns the issue's source.
     *
     * @return string
     */
    public function source(): string
    {
        return $this->request->queryParameter('source', '');
    }

    public function user(): User
    {
        return $this->request->user();
    }
}
