<?php

namespace App\Services\Mail\Builder;

use App\Domain\DTO\Email;
use App\Domain\DTO\Attachment;
use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Builder\MailBuilder;
use App\Services\Mail\Recipient\Recipient;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;

final class Swift implements MailBuilder
{

    /** @var Recipient[] **/
    private $bccRecipients;
    /** @var Recipient[] **/
    private $ccRecipients;
    /** @var Recipient[] **/
    private $mainRecipients;

    public function __construct()
    {
        $this->bccRecipients  = [];
        $this->ccRecipients   = [];
        $this->mainRecipients = [];
    }

    /**
    * @inheritDoc
    */
    public function addToBlindCarbonCopy(EmailAddress $recipient): void
    {
        array_push($this->bccRecipients, $recipient);
    }

    /**
     * @inheritDoc
     */
    public function addToCarbonCopy(EmailAddress $recipient): void
    {
        array_push($this->ccRecipients, $recipient);
    }

    /**
     * @inheritDoc
     */
    public function addToMain(EmailAddress $recipient): void
    {
        array_push($this->mainRecipients, $recipient);
    }

    /**
     * @inheritDoc
     */
    public function build(Email $email)
    {
        $message = new Swift_Message(
            $email->subject()->toString(),
            $email->body()->toString(),
            'text/html',
            'UTF-8'
        );

        array_map(
            function(EmailAddress $emailAddress) use ($message) { $message->addTo($emailAddress->value()); },
            $this->mainRecipients
        );

        array_map(
            function(EmailAddress $emailAddress) use ($message) { $message->addCc($emailAddress->value()); },
            $this->ccRecipients
        );

        array_map(
            function(EmailAddress $emailAddress) use ($message) { $message->addBcc($emailAddress->value()); },
            $this->bccRecipients
        );

        array_map(
            function(Attachment $attachment) use ($message) { $attachment->attachTo($message); },
            $email->attachments()
        );

        return $message;
    }

}
