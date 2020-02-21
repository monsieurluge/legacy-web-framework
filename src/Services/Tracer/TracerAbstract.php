<?php

namespace App\Services\Tracer;

/**
 * TracerAbstract
 */
abstract class TracerAbstract
{

    /** @var int */
    const _DEFAULT = 0;  // trace
    /** @var int */
    const _LOG = 1;  // log
    /** @var int */
    const _WARN = 2;  // warning
    /** @var int */
    const _ERR = 3; // error
    /** @var string */
    const _EMAIL_TRACER = MAIL_ERREUR;
    /** @var string */
    const _EMAIL_TITLE = PROJET;
    /** @var int */
    const _EMAIL_GLOBAL = 1;
    /** @var int */
    const _EMAIL_MAX = 50;
    /** @var string */
    const _DIR_TRACE = 'traces/';

    /**
     * TODO [abstract description]
     * @param string $msg
     * @param int    $type
     */
    abstract public function log($msg, $type = TracerAbstract::_DEFAULT);

    /**
     * Transform level code in string
     * @param int $importanceLevel level of the trace
     */
    protected function setTraceType ($importanceLevel)
    {
        $statut = "trc";

        switch ($importanceLevel) {
            case TracerAbstract::_ERR:
                $statut = "err";
                break;
            case TracerAbstract::_WARN:
                $statut = "warn";
                break;
            case TracerAbstract::_LOG:
                $statut = "log";
                break;
		}

        return $statut;
    }
}
