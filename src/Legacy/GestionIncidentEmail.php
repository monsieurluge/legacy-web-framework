<?php

namespace Legacy;

use Exception;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Mail\Action\CreateIssue;
use App\Services\Mail\Action\Delete;
use App\Services\Mail\Action\Move;
use App\Services\Mail\Action\PrintInfos;
use App\Services\Mail\Action\ProcessNewEMail;
use App\Services\Mail\Action\SaveAttachments;
use App\Services\Mail\Mailbox;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Output\Output;
use App\Services\Templating\TemplateEngine;
use App\Services\Database\DatabaseTools;

/**
 * GestionIncidentEmail
 */
class GestionIncidentEmail
{

    /** @var [type] **/
    protected $dbFactory;
    /** @var [type] **/
    protected $emailService;
    /** @var LoggerInterface */
    protected $logger;
    /** @var Mailer */
    protected $mailer;
    /** @var Output */
    protected $output;
    /** @var TemplateEngine */
    protected $templateEngine;

    /**
     * @param TemplateEngine  $templateEngine
     * @param LoggerInterface $logger
     * @param [type]          $emailService
     * @param Output          $output
     * @param [type]          $dbFactory
     * @param Mailer          $mailer
     */
    public function __construct(
        TemplateEngine $templateEngine,
        LoggerInterface $logger,
        $emailService,
        Output $output,
        $dbFactory,
        Mailer $mailer
    ) {
        $this->dbFactory      = $dbFactory;
        $this->emailService   = $emailService;
        $this->logger         = $logger;
        $this->mailer         = $mailer;
        $this->output         = $output;
        $this->templateEngine = $templateEngine;
        DatabaseTools::$UTF8  = true;
    }

    /**
     * Process some actions on the undeleted emails
     * @param Mailbox $mailbox
     * @throws Exception
     */
    public function processNewEmails(Mailbox $mailbox)
    {
        $this->output->write(str_pad(' Traitement des e-mails entrants', 80, '-', STR_PAD_LEFT));

        $mailbox
            ->open(
                function($message) { $this->connectionSuccess($message); },
                function($message) { $this->connectionError($message); }
            )
            ->count(
                'UNDELETED',
                function($total) {
                    $this->output->write(sprintf('Il y a %s message(s) à traiter', $total));
                }
            )
            ->applyOnTheEMails(
                'UNDELETED',
                new ProcessNewEMail(
                    new PrintInfos($this->output),
                    new CreateIssue(
                        $this->logger,
                        $this->dbFactory,
                        $this->templateEngine,
                        $this->output,
                        $this->mailer
                    ),
                    new SaveAttachments(
                        DOSSIER_UPLOAD,
                        $this->emailService,
                        $this->logger,
                        $this->output
                    ),
                    new Move(
                        MAIL_IMAP_PROCESSED_FOLDER,
                        $this->output
                    ),
                    $this->output,
                    $this->logger
                )
            )
            ->clean(function() { $this->output->write('Boîte de réception nettoyée'); })
            ->close();

        $this->output->write(str_pad(' Fin du traitement des e-mails entrants', 80, '-', STR_PAD_LEFT));
    }

    /**
     * Mailbox connection callback
     * @param string $errorMessage
     */
    private function connectionError(string $errorMessage)
    {
        $message = sprintf(
            'Echec de connexion à la boîte de messages, cause : %s',
            $errorMessage
        );

        $this->logger->log(LogLevel::ERROR, $message);

        $this->output->write($message);
    }

    /**
     * Mailbox connection callback
     * @param string $successMessage
     */
    private function connectionSuccess(string $successMessage)
    {
        $this->output->write(sprintf(
            'Connexion à la boîte de messages OK %s',
            empty($successMessage) ? '' :  sprintf('("%s")', $successMessage)
        ));
    }

}
