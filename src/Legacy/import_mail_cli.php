<?php

require (dirname(__FILE__) . '/../../init.php');

use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Log\LegacyEMailLogger;
use App\Services\Log\LegacyFileLogger;
use App\Services\Mail\Builder\Swift as SwiftMailBuilder;
use App\Services\Mail\ClosedMailbox;
use App\Services\Mail\LegacyMailService;
use App\Services\Mail\Mailer\MailerFactory;
use App\Services\Output\ConsoleOutput;
use App\Services\Templating\SmartyEngine;
use App\Services\Database\DatabaseFactory;
use Legacy\GestionIncidentEmail;

$_SERVER['DOCUMENT_ROOT'] = PROJECT_ROOT;

$emailService = new LegacyMailService();
$logger       = new LegacyFileLogger('import_email');
$smarty       = new Smarty();

$smarty->setCompileDir(DOSSIER_TPL_C);

$gestion = new GestionIncidentEmail(
    new SmartyEngine($smarty),
    new LegacyEMailLogger('', MAIL_DEBUG, true),
    $emailService,
    new ConsoleOutput(),
    new DatabaseFactory($logger),
    (new MailerFactory(new SwiftMailBuilder()))->createFromEnvironment()
);

$mailbox = new ClosedMailbox(
    MAIL_IMAP,
    MAIL_IMAP_PORT,
    MAIL_IMAP_FOLDER,
    MAIL_IMAP_USER,
    MAIL_IMAP_PASSWORD,
    $emailService,
    $logger
);

try {
    $gestion->processNewEmails($mailbox);
} catch (Exception $exception) {
    $message = sprintf(
        'Erreur lors du traitement des emails : %s',
        $exception->getMessage()
    );

    (new ConsoleOutput())->write('[KO] ' . $message);

    $logger->log(LogLevel::ERROR, $message);
}
