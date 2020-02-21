<?php

namespace App\Services\Request\CustomRequest\Api\Post;

use App\Services\Request\Request;

final class CreateReport
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

    public function description()
    {
        return json_decode($this->request->body())->description;
    }

    public function trace()
    {
        return json_decode($this->request->body())->trace;
    }
}
