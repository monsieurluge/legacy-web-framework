<?php

namespace App\ServiceInterfaces\Log;

/**
 * Describes log levels
 */
class LogLevel
{
    /** @var string **/
    const EMERGENCY = 'emergency';
    /** @var string **/
    const ALERT     = 'alert';
    /** @var string **/
    const CRITICAL  = 'critical';
    /** @var string **/
    const ERROR     = 'error';
    /** @var string **/
    const WARNING   = 'warning';
    /** @var string **/
    const NOTICE    = 'notice';
    /** @var string **/
    const INFO      = 'info';
    /** @var string **/
    const DEBUG     = 'debug';
}
