<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;

final class UpdateNote
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
}
