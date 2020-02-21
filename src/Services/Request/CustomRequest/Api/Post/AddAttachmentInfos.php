<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class AddAttachmentInfos
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

    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }

    public function user(): User
    {
        return $this->request->user();
    }
}
