<?php

namespace App\Services\Mail;

use PDO;
use App\Domain\DTO\Attachment;
use App\Domain\DTO\Email;
use App\Domain\DTO\FileAttachment;
use App\Domain\DTO\EmbedAttachment;
use App\Domain\ValueObject\Label;
use App\Services\Database\Database;
use App\Services\Templating\TemplateEngine;
use App\Services\Text\SimpleText;

final class EmailFromRequest
{

    /** @var Database **/
    private $issueDatabase;
    /** @var int **/
    private $issueId;
    /** @var TemplateEngine **/
    private $templateEngine;
    /** @var array **/
    private $attachmentsPOST;
    /** @var string **/
    private $subjectPOST;
    /** @var string **/
    private $bodyPOST;

    public function __construct(int $issueId, array $attachmentsPOST, string $subjectPOST, string $bodyPOST, Database $issueDatabase, TemplateEngine $templateEngine)
    {
        $this->attachmentsPOST = $attachmentsPOST;
        $this->subjectPOST     = $subjectPOST;
        $this->bodyPOST        = $bodyPOST;
        $this->issueDatabase   = $issueDatabase;
        $this->issueId         = $issueId;
        $this->templateEngine  = $templateEngine;
    }

    /**
     * TODO [attachment description]
     *
     * @param int $attachmentID
     * @return mixed
     */
    private function attachment(int $attachmentID)
    {
        $result = $this->issueDatabase->query(
            'SELECT * FROM incident_pj WHERE incident_id = :id AND id = :pj ORDER BY id ASC',
            PDO::FETCH_ASSOC,
            [ 'id' => $this->issueId, 'pj' => $attachmentID ]
        );

        if (count($result) === 1) {
            return $result[0];
        }

        return null;
    }

    /**
     * Returns the request's attachments
     *
     * @return Attachment[]
     */
    private function attachmentsFromPOST(): array
    {
        $attachments = [];

        foreach ($this->attachmentsPOST as $attachmentID) {
            $dbAttachment = $this->attachment($attachmentID);

            $pathFile = file_exists(DOSSIER_UPLOAD . $dbAttachment['pj'])
                ? DOSSIER_UPLOAD . $dbAttachment['pj']
                : DOSSIER_UPLOAD . $this->issueId . '/' . $dbAttachment['pj'];

            $attachments[] = new FileAttachment(
                new SimpleText($dbAttachment['pj']),
                new SimpleText($pathFile)
            );
        }

        return $attachments;
    }

    /**
     * Returns the request's email content
     *
     * @return Email
     */
    public function create(): Email
    {
        $attachments = array_merge(
            $this->attachmentsFromPOST(),
            [
                new EmbedAttachment(
                    new SimpleText('logo_legacy.gif'),
                    new SimpleText(DOSSIER_TPL_IMAGES . 'logo_legacy.gif'),
                    new Label('#cid-legacy-logo#')
                )
            ]
        );

        $this->templateEngine
            ->setTemplateDir(DOSSIER_TPL)
            ->assign([
                'attachments' => array_map(function($attachment) { return $attachment->name()->toString(); }, $attachments),
                'content' => $this->bodyPOST
            ]);

        return new Email(
            new SimpleText(stripslashes($this->subjectPOST)),
            new SimpleText($this->templateEngine->fetch('incident_email.tpl')),
            $attachments
        );
    }


}
