<?php

namespace App\Services\Request\CustomRequest\Page;

use App\Services\Request\Request;

final class CriticalError
{
    /** @var string **/
    private $trace;

    /**
     * @codeCoverageIgnore
     * @param string $trace
     */
    public function __construct(string $trace)
    {
        $this->trace = $trace;
    }

    /**
     * Returns the trace.
     *
     * @return string
     */
    public function trace(): string
    {
        return $this->trace;
    }
}
