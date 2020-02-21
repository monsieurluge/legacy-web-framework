<?php

namespace App\Services\Log;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Log\AbstractLogger;
use App\Services\Tracer\TracerAbstract;
use App\Services\Tracer\TracerFile;

/**
 * [final description]
 * @var [type]
 */
final class LegacyFileLogger extends AbstractLogger implements LoggerInterface
{

    /** @var TracerFile **/
    private $legacyLogger;

    /**
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->legacyLogger = new TracerFile(true, false, '', $fileName);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        switch($level) {
            case LogLevel::INFO:
            case LogLevel::DEBUG:
            case LogLevel::NOTICE:
                $legacyLevel = TracerAbstract::_LOG;
                break;
            case LogLevel::WARNING:
                $legacyLevel = TracerAbstract::_WARN;
                break;
            case LogLevel::ALERT:
            case LogLevel::CRITICAL:
            case LogLevel::EMERGENCY:
            case LogLevel::ERROR:
                $legacyLevel = TracerAbstract::_ERR;
                break;
            default:
                $legacyLevel = TracerAbstract::_DEFAULT;
                break;
        }

        $this->legacyLogger->log($message, $legacyLevel);
    }

}
