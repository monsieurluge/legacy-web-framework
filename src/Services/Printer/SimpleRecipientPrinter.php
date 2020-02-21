<?php

namespace App\Services\Printer;

use App\Domain\ValueObject\EmailAddress;
use App\Services\Printer\RecipientPrinter;
use App\Services\Result\Option;
use App\Services\Result\Some;

final class SimpleRecipientPrinter implements RecipientPrinter
{

    /** @var string[] **/
    private $bccRecipients;
    /** @var string[] **/
    private $ccRecipients;
    /** @var string[] **/
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
    public function printBcc(EmailAddress $address, Option $name): void
    {
        array_push(
            $this->bccRecipients,
            $this->formattedInformation($address, $name)
        );
    }

    /**
    * @inheritDoc
    */
    public function printCc(EmailAddress $address, Option $name): void
    {
        array_push(
            $this->ccRecipients,
            $this->formattedInformation($address, $name)
        );
    }

    /**
    * @inheritDoc
    */
    public function printTo(EmailAddress $address, Option $name): void
    {
        array_push(
            $this->mainRecipients,
            $this->formattedInformation($address, $name)
        );
    }

    public function bccAsString(): string
    {
        return empty($this->bccRecipients)
            ? '--'
            : implode(', ', $this->bccRecipients);
    }

    public function ccAsString(): string
    {
        return empty($this->ccRecipients)
            ? '--'
            : implode(', ', $this->ccRecipients);
    }

    public function mainAsString(): string
    {
        return empty($this->mainRecipients)
            ? '--'
            : implode(', ', $this->mainRecipients);
    }

    /**
     * Returns the recipient's informations as follows:
     *   - "foo@bar.ok (Recipient Name)" if a name is supplied
     *   - "foo@bar.ok" otherwise
     *
     * @param  EmailAddress $address
     * @param  Option       $name
     * @return string
     */
    private function formattedInformation(EmailAddress $address, Option $name): string
    {
        return sprintf(
            '%s%s',
            $address->value(),
            $name
                ->map(function(string $name) { return new Some(sprintf('(%s)', $name)); })
                ->getContentOrDefaultOnFailure('')
        );
    }

}
