<?php

namespace App\Services\Mail\Mailer;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/* * ******************************************
  Send an email
  1.1
 * ****************************************** */

//  V1.1 - 20120709 : javadoc, add function goodExecute()
//  V1.0 - 20120323 : creation and integration in Strategy (Logger/Mailer) pattern

/* * ******************************************************************************************************************************************************************
  Utilisation
  $mailer = new Mailer("erreur@sirees.com", "support@sirees.com", "Heracles:  Erreur SQL" , "TEST de contenu", array(), false);
 * ****************************************************************************************************************************************************************** */

/**
 * Mailer
 */
class LegacyMailer
{

    /** @var string */
    private $_email;
    /** @var bool */
    private $execute;

    /**
     * Constructeur
     * @param string $from emetteur
     * @param string $tox destinataires, separer par un point virgule, ou dans un tableau
     * @param string $subject titre du message
     * @param string $body corps du message
     * @param array $filesx tableau de pièce jointe
     * @param bool $legacy_body ajout le corps legacy
     */
    public function __construct($from, $tox, $subject, $body, $filesx = array(), $legacy_body = true)
    {
        $message_hj = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($tox);

        if ($legacy_body == true) {
            $body = $message_hj->setBody($this->genLegacyBody($body), 'text/html');
        } else {
            $message_hj->setBody($body);
        }

        foreach ($filesx as $f) {
            $message_hj->attach(Swift_Attachment::fromPath($f));
        }

        $transport = Swift_SmtpTransport::newInstance(MAIL_SMTP, MAIL_SMTP_PORT, MAIL_SMTP_SECURITY)
            ->setUsername(MAIL_SMTP_USER)
            ->setPassword(MAIL_SMTP_PASSWORD);

        $mailer = Swift_Mailer::newInstance($transport);

        $this->execute = $mailer->send($message_hj,$failure);
    }

    /**
     * TODO [genLegacyBody description]
     * @param  string $body
     * @return string
     */
    public function genLegacyBody($body)
    {
        $logoB64 = base64_encode(file_get_contents(MAIL_LOGO_PATH));

        return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
a {
color: #360b05;
}
p {
margin: 1em 0; font-family : "verdana"; font-size: 0.7em; color: #360b05;
}
.petit {
margin: 1em 0; font-family : "verdana"; font-size: 0.6em; color: #360b05;
}
h1 {
font-size: 1.5em;
font-family: verdana;
color: #360b05;
padding-bottom: 2%;
padding-top: 2%;
text-align: center;
padding-right: 20%;
padding-left: 30%;
}

html, body {
height: 100%;
width: 100%;
}

center {
text-align: center;
}
-->
</style>

</head><body>
<table>
<tr><td width="30%" >
<span align="center">
<img src="data:image/png;base64,' . $logoB64 . '">
</span>
<br>
<p class="petit"><b>Support LEGACY</b></p>
<p class="petit">Hotline :<br>
04 67 61 70 90</p>
<p class="petit">Mail du support :<br>
<a href="mailto:support@legacy-asso.com">support@legacy-asso.com</a></p>
<br>
<p class="petit">Accueil téléphonique<br>
du lundi au vendredi<br>
de 09h00 à 12h30 et<br>
de 13h30 à 17h30</p>

</td><td>
' . $body . '
</td></tr>
</table></body></html>';
    }

    /**
     * Retourne le bon déroulement de l'envoie
     * @return bool
     */
    public function good_execute()
    {
        return $this->execute;
    }

}
