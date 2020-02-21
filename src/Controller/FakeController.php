<?php

namespace App\Controller;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fake Controller, for test purpose only
 * @codeCoverageIgnore
 */
final class FakeController
{
    /** @var array **/
    private $calls;
    /** @var Response **/
    private $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->calls    = [];
        $this->response = $response;
    }

    /**
     * Returns how many times the function "process" was called.
     *
     * @return int
     */
    public function called(): int
    {
        return count($this->calls);
    }

    /**
     * Returns the "process" function calls.
     * Each item is a serialized Request.
     *
     * @return array
     */
    public function callStack(): array
    {
        return $this->calls();
    }

    /**
     * @inheritDoc
     */
    public function process($request): Result
    {
        $this->calls[] = serialize($request);

        return new Success($this->response);
    }
}
