<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\DTO\FileAttachment;
use App\Domain\DTO\Email;
use App\Services\File\CsvFile;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Recipient;
use App\Services\Templating\TemplateEngine;
use App\Services\Text\SimpleText;
use App\Services\Text\Text;

/**
 * Sends the CSV export by mail.
 */
final class SendCSVExportMail implements Action
{
    /** @var CsvFile **/
    private $file;
    /** @var Mailer **/
    private $mailer;
    /** @var TemplateEngine **/
    private $templateEngine;

    /**
     * @param Mailer         $mailer
     * @param TemplateEngine $templateEngine
     * @param CsvFile        $file
     */
    public function __construct(Mailer $mailer, TemplateEngine $templateEngine, CsvFile $file)
    {
        $this->file           = $file;
        $this->mailer         = $mailer;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @inheritDoc
     * @param Recipient[] $target
     */
    public function handle($target): Result
    {
        $this->mailer->sendEmailTo(
            new Email(
                new SimpleText('[LEGACY] Export CSV'),
                $this->emailContent(),
                [
                    new FileAttachment(
                        new SimpleText($this->file->name()),
                        new SimpleText($this->file->path())
                    )
                ]
            ),
            $target
        );

        return new Success($target);
    }

    /**
     * Returns the email content
     *
     * @return Text
     */
    private function emailContent(): Text
    {
        $this->templateEngine
            ->setTemplateDir(DOSSIER_TPL)
            ->assign([
                'texte' => 'Veuillez trouver en pièce jointe l\'export CSV que vous avez demandé.'
            ]);

        return new SimpleText(
            $this->templateEngine->fetch('csv_export_email.tpl')
        );
    }
}
