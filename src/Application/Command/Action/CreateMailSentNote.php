<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\DTO\Attachment;
use App\Domain\DTO\Email;
use App\Domain\ValueObject\ID;
use App\Repository\Notes as NotesRepository;
use App\Services\Mail\Recipient\Recipient;
use App\Services\Printer\SimpleRecipientPrinter;
use App\Services\Security\User\User;
use Legacy\Incident;

/**
 * Creates a "mail sent" note.
 */
final class CreateMailSentNote implements Action
{
    /** @var Email **/
    private $email;
    /** @var NotesRepository **/
    private $notesRepository;
    /** @var string[] **/
    private $recipients;
    /** @var User **/
    private $user;

    /**
     * @param NotesRepository $notesRepository
     * @param User            $user
     * @param Recipient[]     $recipients
     * @param Email           $email
     * @param string          $rawBody
     */
    public function __construct(NotesRepository $notesRepository, User $user, array $recipients, Email $email, string $rawBody)
    {
        $this->email           = $email;
        $this->notesRepository = $notesRepository;
        $this->rawBody         = $rawBody;
        $this->recipients      = $recipients;
        $this->user            = $user;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     */
    public function handle($target): Result
    {
        $printer = new SimpleRecipientPrinter();

        array_map(
            function(Recipient $recipient) use ($printer) { $recipient->print($printer); },
            $this->recipients
        );

        return $this->notesRepository
            ->create(
                new ID($target->id),
                $this->user,
                sprintf(
                    'Email envoyé : ' . PHP_EOL
                        . 'À : %s' . PHP_EOL
                        . 'CC : %s' . PHP_EOL
                        . 'PJs : %s' . PHP_EOL
                        . 'Contenu : %s',
                    $printer->mainAsString(),
                    $printer->ccAsString(),
                    $this->attachmentsAsString(),
                    $this->rawBody
                )
            )
            ->then(function () use ($target) { return new Success($target); });
    }

    /**
     * Returns the attachments names as a single line string
     *
     * @return string
     */
    private function attachmentsAsString(): string
    {
        return empty($this->email->attachments())
            ? '--'
            : implode(
                array_map(
                    function(Attachment $attachment) { return $attachment->name()->toString(); },
                    $this->email->attachments()
                ),
                '; '
            );
    }
}
