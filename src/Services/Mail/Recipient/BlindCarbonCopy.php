<?php

namespace App\Services\Mail\Recipient;

use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Builder\MailBuilder;
use App\Services\Mail\Recipient\Recipient;
use App\Services\Printer\RecipientPrinter;
use App\Services\Result\None;

final class BlindCarbonCopy implements Recipient
{

    /** @var EmailAddress **/
    private $emailAddress;

    /**
     * @param EmailAddress $emailAddress
     */
    public function __construct(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @inheritDoc
     */
    public function addTo(MailBuilder $builder): void
    {
        $builder->addToBlindCarbonCopy($this->emailAddress);
    }

    /**
    * @inheritDoc
    */
    public function print(RecipientPrinter $printer): void
    {
        $printer->printBcc($this->emailAddress, new None());
    }

}
