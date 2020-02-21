<?php

namespace App\Services\Mail\Mailer;

use Exception;
use App\Domain\DTO\Email;
use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Builder\MailBuilder;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Recipient;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;
use Swift_Transport;

/**
 * Swift Mailer (immutable object)
 */
final class SwiftMailer implements Mailer
{

    /** @var MailBuilder **/
    private $mailBuilder;
    /** @var EmailAddress **/
    private $sender;
    /** @var string **/
    private $senderName;
    /** @var Swift_Transport **/
    private $transport;

    /**
     * @param MailBuilder     $mailBuilder
     * @param Swift_Transport $transport
     * @param EmailAddress    $sender
     * @param string          $senderName
     */
    public function __construct(MailBuilder $mailBuilder, Swift_Transport $transport, EmailAddress $sender, string $senderName)
    {
        $this->mailBuilder = $mailBuilder;
        $this->sender      = $sender;
        $this->senderName  = $senderName;
        $this->transport   = $transport;
    }

    /**
     * @inheritDoc
     */
    public function sendEmailTo(Email $email, array $recipients): void
    {
        array_map(
            function($recipient) { $recipient->addTo($this->mailBuilder); },
            $recipients
        );

        $message = $this->mailBuilder
            ->build($email)
            ->setSender($this->sender->value(), $this->senderName);

        (new Swift_Mailer($this->transport))->send($message);
    }

}
