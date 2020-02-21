<?php

namespace App\Services\Log;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Log\FakeLogger;
use App\Services\Log\AbstractLogger;

/**
 * File Logger Factory (immutable object)
 *
 * Allows to create a File Logger, depending on the running environment.
 * Ex: when in DEV, it creates a FakeLogger, in PROD it creates a FileLogger
 */
final class FileLoggerFactory extends AbstractLogger implements LoggerInterface
{

    /** @var string **/
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        $this->createFromEnvironment()->log($level, $message, $context);
    }

    /**
     * Returns a Logger depending on the running environment
     *
     * @return LoggerInterface
     */
    private function createFromEnvironment(): LoggerInterface
    {
        switch (strtolower(ENVIRONMENT)) {
            case 'local':
            case 'dev':
            case 'development':
            case 'test':
                return new FileLogger(LOGS_FOLDER . $this->fileName);
                break;
            case 'prod':
            case 'production':
            default:
                return new FakeLogger(
                    new class {
                        public function setMessage() {}
                    }
                );
                break;
        }
    }

}
