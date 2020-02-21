<?php

namespace App\Services\Mail\Action;

use App\ServiceInterfaces\Log\LogLevel;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;
use App\Services\Output\Output;

/**
 * "Process New e-mail" Action (immutable object).
 * Sequentially prints the email informations (step 1)
 *   then creates or updates the issue (step 2)
 *   then saves the email attachments (step 3)
 *   then moves the email to the "PROCESSED" mailbox folder (step 4)
 * If there is a failure during the steps 2 & 3 the email is moved to another mailbox.
 */
final class ProcessNewEMail extends AbstractAction implements Action
{

    /** @var Action **/
    private $createIssueAction;
    /** @var Action **/
    private $onProcessedAction;
    /** @var Action **/
    private $printInfosAction;
    /** @var Action **/
    private $saveAttachmentsAction;
    /** @var LoggerInterface **/
    private $logger;
    /** @var Output **/
    private $output;

    /**
     * @param Action          $printInfos
     * @param Action          $createIssue
     * @param Action          $saveAttachments
     * @param Action          $onProcessed
     * @param Output          $output
     * @param LoggerInterface $logger
     */
    public function __construct(
        Action $printInfos,
        Action $createIssue,
        Action $saveAttachments,
        Action $onProcessed,
        Output $output,
        LoggerInterface $logger
    ) {
        $this->createIssueAction     = $createIssue;
        $this->onProcessedAction     = $onProcessed;
        $this->logger                = $logger;
        $this->output                = $output;
        $this->printInfosAction      = $printInfos;
        $this->saveAttachmentsAction = $saveAttachments;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        $this->printInfos($mailbox, $email);

        return $this;
    }

    /**
     * Prints the e-mail informations
     * @param Mailbox $mailbox
     * @param EMail   $email
     */
    private function printInfos(Mailbox $mailbox, EMail $email)
    {
        $this->printInfosAction
            ->handle($mailbox, $email)
            ->then(function() use ($mailbox, $email) { $this->createIssue($mailbox, $email); })
            ->else(function($error) { $this->output->write('printInfos() -> ' . $error); });
    }

    /**
     * Creates the issue
     * @param Mailbox $mailbox
     * @param EMail   $email
     */
    private function createIssue(Mailbox $mailbox, EMail $email)
    {
        $this->createIssueAction
            ->handle($mailbox, $email)
            ->then(function ($result) use ($mailbox, $email) { $this->saveAttachments($mailbox, $email, $result); })
            ->else(function ($error) use ($mailbox, $email) { $this->creationFailed($mailbox, $email, $error); });
    }

    /**
     * Actions done if the issue creation failed
     *
     * @param Mailbox $mailbox
     * @param EMail   $email
     * @param string  $error
     */
    private function creationFailed(Mailbox $mailbox, EMail $email, string $error)
    {
        $this->output->write(' [ERREUR] création d\'un incident : ' . $error);

        // move the e-mail to a "failures" mailbox folder
        (new Move(MAIL_IMAP_ERROR_FOLDER, $this->output))->handle($mailbox, $email);

        $this->logger->log(
            LogLevel::ERROR,
            sprintf(
                'erreur lors de la création d\'un incident à partir du mail %s : %s',
                $email->subject(),
                $error
            )
        );
    }

    /**
     * Saves the attachments to the disk
     * @param Mailbox $mailbox
     * @param EMail   $email
     * @param int     $issueID
     */
    private function saveAttachments(Mailbox $mailbox, EMail $email, int $issueID)
    {
        $this->saveAttachmentsAction
            ->withParameter($issueID)
            ->handle($mailbox, $email)
            ->then(function () use ($mailbox, $email) { $this->onProcessed($mailbox, $email); })
            ->else(function ($error) use ($issueID, $mailbox, $email) {
                (new Move(MAIL_IMAP_ERROR_FOLDER, $this->output))->handle($mailbox, $email);

                $this->output->write('[ERREUR] sauvegarde des pièces jointes : ' . $error);
                $this->logger->log(sprintf(
                    'erreur lors de la sauvegarde des pièces jointes pour l\'incident %s : %s',
                    $issueID,
                    $error
                ));
            });
    }

    /**
     * Applies the "on processed" action
     *
     * @param Mailbox $mailbox
     * @param EMail   $email
     */
    private function onProcessed(Mailbox $mailbox, EMail $email)
    {
        $this->onProcessedAction
            ->handle($mailbox, $email)
            ->else(function ($error) use ($email) {
                $this->output->write('[ERREUR] traitement d\'un e-mail : ' . $error);
                $this->logger->log(sprintf(
                    'erreur lors du traitement de l\'e-mail %s : %s',
                    $email->subject(),
                    $error
                ));
            });
    }

}
