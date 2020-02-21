<?php

namespace App\Services\Log;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Log\AbstractLogger;
use App\Services\Tracer\TracerAbstract;
use App\Services\Tracer\TracerEmail;

/**
 * "Tracer EMail" wrapper
 */
final class LegacyEMailLogger extends AbstractLogger implements LoggerInterface
{

    /** @var TracerEmail **/
    private $legacyLogger;

    /**
     * @param string $subtitle
     * @param string $recipient
     * @param bool   $global
     */
    public function __construct(string $subtitle, string $recipient, bool $global = false)
    {
        $this->legacyLogger = new TracerEmail($subtitle, $recipient, $global);
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
