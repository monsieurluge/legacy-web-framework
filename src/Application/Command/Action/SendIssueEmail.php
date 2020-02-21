<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\DTO\Email;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Recipient;
use App\Services\Text\IssueEmailSubject;
use Legacy\Incident;

/**
 * Sends an issue by mail.
 */
final class SendIssueEmail implements Action
{
    /** @var Email **/
    private $email;
    /** @var Mailer **/
    private $mailer;
    /** @var Recipient[] **/
    private $recipients;

    /**
     * @param Mailer      $mailer
     * @param Email       $email
     * @param Recipient[] $recipients
     */
    public function __construct(Mailer $mailer, Email $email, array $recipients)
    {
        $this->email      = $email;
        $this->mailer     = $mailer;
        $this->recipients = $recipients;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     */
    public function handle($target): Result
    {
        $this->mailer->sendEmailTo(
            new Email(
                new IssueEmailSubject($target, $this->email),
                $this->email->body(),
                $this->email->attachments()
            ),
            $this->recipients
        );

        return new Success($target);
    }
}
