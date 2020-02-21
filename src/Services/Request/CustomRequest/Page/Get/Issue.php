<?php

namespace App\Services\Request\CustomRequest\Page\Get;

use App\Services\Request\Request;
use App\Services\Security\User\User;

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

    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }

    public function userId(): int
    {
        return intval($this->request->user()->toArray()['id']);
    }

    public function userLastName(): string
    {
        return $this->request->user()->toArray()['lastname'];
    }
}
