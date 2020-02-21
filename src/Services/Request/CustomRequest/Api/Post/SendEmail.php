<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;
use App\Services\Security\User\User;

/**
 * "send an e-mail" HTTP request data.
 */
final class SendEmail
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
     * Returns the target issue ID.
     *
     * @return int
     */
    public function issueId(): int
    {
        return intval($this->request->pathParameter('id'));
    }

    /**
     * Returns the logged User.
     *
     * @return User
     */
    public function user(): User
    {
        return $this->request->user();
    }
}
