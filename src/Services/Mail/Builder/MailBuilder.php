<?php

namespace App\Services\Mail\Builder;

use App\Domain\DTO\Email;
use App\Domain\ValueObject\EmailAddress;

interface MailBuilder
{

    /**
    * Adds a recipient to the "blind carbon copy" ones
    *
    * @param EmailAddress $recipient
    */
    public function addToBlindCarbonCopy(EmailAddress $recipient): void;

    /**
     * Adds a recipient to the "carbon copy" ones
     *
     * @param EmailAddress $recipient
     */
    public function addToCarbonCopy(EmailAddress $recipient): void;

    /**
     * Adds a recipient to the main ones
     *
     * @param EmailAddress $recipient
     */
    public function addToMain(EmailAddress $recipient): void;

    /**
     * Returns the message to send
     *
     * @param Email $email
     * @return [type]
     */
    public function build(Email $email);

}
