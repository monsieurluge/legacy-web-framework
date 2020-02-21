<?php

namespace App\Services\Tracer;

use App\Services\Mail\Mailer\LegacyMailer as Mailer;
use App\Services\Tracer\TracerAbstract;

/**
 * TracerEmail
 * Utilisation
 * $loggerEmail = new LoggerEmail("Trace");
 * $loggerEmail->log("Erreur de traces", "erreur@sirees.com", 3);
 */
class TracerEmail extends TracerAbstract
{

    /** @var [type] */
    private $_emailDestination;
    /** @var [type] */
    private $_global;
    /** @var [type] */
    private $_nbMax;
    /** @var [type] */
    private $_subtitle;
    /** @var [type] */
    private $_waitingEmail;
    /** @var [type] */
    private $_nbSendEmail;
    /** @var [type] */
    private $_file;
    /** @var [type] */
    private $deep;

    /**
     * Constructeur
     * @param string $subtitle Change le sous-titre du mail
     * @param string $emailDestination Change le mail du destinataire (sinon celui definit par la global)
     * @param bool   $global regroupe l'envoi des mail en un seul
     * @param int    $nbMaxEmail indique le nombre maximum de mail à envoyé (0 = pas de limite, inutile si $global = true)
     * @param int    $deeper
     */
    public function __construct($subtitle = '', $emailDestination = '', $global = false, $nbMaxEmail = 0, $deeper = 0)
    {
        if ($emailDestination == '') {
            $emailDestination = $this->getEmailDestination();
        }

        $this->deep              = $deeper;
        $this->_global           = $global;
        $this->_emailDestination = $emailDestination;
        $this->_nbMax            = $nbMaxEmail;
        $this->_subtitle         = $subtitle;
        $this->_nbSendEmail      = 0;

        $deep       = $this->deep;
        $historique = debug_backtrace();

        if (!isset($historique[$deep])) {
            $deep = count($historique) - 1;
        }

        if (isset($historique[$deep]['file'])) {
            $this->_file = $historique[$deep]['file'];
        }

        if ($global === true) {
            $this->_waitingEmail = array();
        }

        register_shutdown_function(array($this, 'destruct'));
    }

    /**
     * destructeur
     */
    public function destruct()
    {
        if ($this->_global && is_array($this->_waitingEmail) && count($this->_waitingEmail)>0) {
            $this->sendEmail("Fichier " . $this->_file . " : \r\n " . implode("\r\n", $this->_waitingEmail));
        }
    }

    /**
     * Trace une information selon le fichier émmeteur
     * @param string $msg le message a enregistré.
     * @param int $type Logger::_DEFAULT, Logger::_WARN, Logger::_ERR, Logger::_LOG
     */
    public function log($msg, $type = TracerAbstract::_DEFAULT)
    {
        $historique = debug_backtrace();
        $deep       = $this->deep;

        if (!isset($historique[$deep])) {
            $deep = count($historique) - 1;
        }

        $line = '	';

        if (isset($historique[$deep]['line'])) {
            $line = '('.$historique[$deep]['line'].')';
        }

        if (!is_string($type)) {
            $type = $this->setTraceType($type);
        }

        $message = str_pad(date("H:i:s"), 8, ' ') . '	' . str_pad(substr($type, 0, 4), 4, ' ') . $line . ': ' . $msg;

        if (!$this->_global) {
            $allowsend = true;

            if ($this->_nbMax != 0 && $this->_nbSendEmail >= $this->_nbMax) {
                $allowsend = false;
            }

            $globalnbmax = TracerAbstract::_EMAIL_MAX;

            if ($globalnbmax !== null && $globalnbmax != 0 && $this->_nbSendEmail >= $globalnbmax) {
                $allowsend   = false;
            }

            if ($allowsend) {
                $this->_nbSendEmail++;
                $this->sendEmail("Fichier " . $this->_file . " : \r\n " . $message);
            }
        } else {
            $this->_waitingEmail[] = $message;
        }
    }

    /**
     * TODO [sendEmail description]
     * @param  string $message
     */
    private function sendEmail($message)
    {
        $mainTitle = sprintf('%s(%s)', TracerAbstract::_EMAIL_TITLE, strtolower(ENVIRONMENT));
        $title	   = '';

        if ($mainTitle !== null && $mainTitle !== '' && $this->_subtitle !== '') {
            $title	 = $mainTitle . ' : ' . $this->_subtitle;
        } elseif ($mainTitle !== null && $mainTitle !== '') {
            $title	 = $mainTitle;
        } else {
            $title	 = $this->_subtitle;
        }

        new Mailer(
            MAIL_SENDER_EMAIL,
            $this->_emailDestination,
            $title,
            $message, array(), false
        );
    }

}
