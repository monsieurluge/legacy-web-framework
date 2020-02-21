<?php

namespace App\Services\Mail\Mailer;

use App\Domain\DTO\Email;
use App\Services\Mail\Recipient\Recipient;

/**
 * Mailer interface
 */
interface Mailer
{

    /**
     * Sends the e-mail to the given recipients
     *
     * @param Email       $email
     * @param Recipient[] $recipients
     */
    public function sendEmailTo(Email $email, array $recipients): void;

}
