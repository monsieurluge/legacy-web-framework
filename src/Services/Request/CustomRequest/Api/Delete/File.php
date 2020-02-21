<?php

namespace App\Services\Request\CustomRequest\Api\Delete;

use App\Services\Request\Request;
use App\Services\Security\User\User;

final class File
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
     * Returns the file's ID.
     *
     * @return int
     */
    public function fileId(): int
    {
        return intval($this->request->pathParameter('fileId'));
    }

    public function user(): User
    {
        return $this->request->user();
    }
}
