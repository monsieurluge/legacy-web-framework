<?php

namespace App\Services\Log;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Log\AbstractLogger;

/**
 * File Logger
 */
final class FileLogger extends AbstractLogger implements LoggerInterface
{

    /** @var string **/
    private $file;

    /**
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        file_put_contents(
            $this->file,
            sprintf('[%s] %s', (new \DateTime())->format('H:m'), $message) . PHP_EOL,
            FILE_APPEND
        );
    }

}
