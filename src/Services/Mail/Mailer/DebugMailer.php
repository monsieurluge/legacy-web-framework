<?php

namespace App\Services\Mail\Mailer;

use App\Domain\DTO\Email;
use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Mailer\Mailer;
use App\Services\Mail\Recipient\Main as MainRecipient;
use App\Services\Mail\Recipient\Recipient;

/**
 * Debug Mailer (immutable object, Mailer decorator)
 * Always send the e-mail to the recipient defined as MAIL_DEBUG
 */
final class DebugMailer implements Mailer
{

    /** @var string **/
    private $debugRecipient;
    /** @var Mailer **/
    private $origin;

    /**
     * @param Mailer $origin the Mailer to decorate
     */
    public function __construct(Mailer $origin)
    {
        $this->debugRecipient = MAIL_DEBUG;
        $this->origin         = $origin;
    }

    /**
     * @inheritDoc
     */
    public function sendEmailTo(Email $email, array $recipients): void
    {
        $this->origin->sendEmailTo(
            $email,
            [
                new MainRecipient(
                    new EmailAddress($this->debugRecipient)
                )
            ]
        );
    }

}
