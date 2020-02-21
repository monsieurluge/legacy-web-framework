<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class CreateNote
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

    public function content()
    {
        return htmlentities(json_decode($this->request->body())->content, ENT_QUOTES);
    }

    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }

    public function user(): User
    {
        return $this->request->user();
    }

    public function userId(): int
    {
        return intval($this->request->user()->toArray()['id']);
    }
}
