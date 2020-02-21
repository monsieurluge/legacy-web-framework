<?php

namespace App\Services\Mail\Action;

use \DateTime;
use App\Services\Database\DatabaseTools;
use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\Action\Success;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\LegacyMailService;
use App\Services\Mail\Mailbox;
use App\Services\Output\Output;

/**
 * "Save e-mail Attachments" Action (immutable object)
 */
final class SaveAttachments extends AbstractAction implements Action
{

    /** @var LegacyMailService **/
    private $eMailService;
    /** @var string **/
    private $folder;
    /** @var int|null **/
    private $issueID;
    /** @var [type] **/
    private $logger;
    /** @var Output **/
    private $output;

    /**
     * @param string            $folder
     * @param LegacyMailService $eMailService
     * @param [type]            $logger
     * @param Output            $output
     * @param int|null          $issueID
     */
    public function __construct(
        string $folder,
        LegacyMailService $eMailService,
        $logger,
        Output $output,
        $issueID = null
    ) {
        $this->eMailService = $eMailService;
        $this->folder       = $folder;
        $this->issueID      = $issueID;
        $this->logger       = $logger;
        $this->output       = $output;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        if ($this->isAutoResponse($email) || count($email->attachments()) === 0 ) {
            $this->output->write(' > aucune pièce jointe à enregistrer');

            return new Success($this, $this->issueID);
        }

        if (!file_exists($this->folder)) {
            mkdir($this->folder);
            chmod($this->folder, 0770);
        }

        $created = (new DateTime('now'))->format('Y-m-d H:i:s');

        foreach ($email->attachments() as $attachment) {
            $this->output->write(sprintf(
                ' > traitement de la pièce jointe "%s"',
                $attachment['name']
            ));

            // write the attachment to the disk
            $this->eMailService->saveAttachment(
                $mailbox,
                $email->uid(),
                $attachment['partNum'],
                $attachment['enc'],
                $this->folder . '/' . $attachment['name']
            );

            // link the attachment to the issue
            $data = [
                'incident_id' => $this->issueID,
                'date'        => $created,
                'utilisateur' => null,
                'pj'          => $attachment['name']
            ];

            DatabaseTools::insert(DB_INCIDENT['base'], $this->logger, 'incident_pj', $data, true, true);
        }

        return new Success($this, $this->issueID);
    }

    /**
     * @inheritDoc
     */
    public function withParameter($parameter): Action
    {
        if ($parameter) {
            return new self(
                sprintf('%s/%s', $this->folder, $parameter),
                $this->eMailService,
                $this->logger,
                $this->output,
                $parameter
            );
        }

        return $this;
    }

    /**
     * Checks if the e-mail is an "auto-response" one
     * @param EMail $email
     * @return bool
     */
    private function isAutoResponse(EMail $email): bool
    {
        return (
            in_array($email->subject(), [ '** Réponse Automatique **' ]) !== false
            || strpos(strtolower($email->subject()), 'réponse automatique') !== false
        );
    }

}
