<?php

namespace App\Services\Log;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Log\AbstractLogger;

/**
 * Fake Logger, for test purpose only
 * @codeCoverageIgnore
 */
final class FakeLogger extends AbstractLogger implements LoggerInterface
{

    /** @var object **/
    private $target;

    /**
     * @param object $target an object that must expose the method "setMessage(string *)"
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array()): LoggerInterface
    {
        $this->target->setMessage($message);

        return $this;
    }

}
