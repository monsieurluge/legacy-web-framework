<?php

namespace App\Services\Printer;

use App\Domain\ValueObject\EmailAddress;
use App\Services\Result\Option;

interface RecipientPrinter
{

    /**
     * Prints the "blind carbon copy" recipient informations
     *
     * @param EmailAddress $address
     * @param Option       $name
     */
    public function printBcc(EmailAddress $address, Option $name): void;

    /**
     * Prints the "carbon copy" recipient informations
     *
     * @param EmailAddress $address
     * @param Option       $name
     */
    public function printCc(EmailAddress $address, Option $name): void;

    /**
     * Prints the "main / to" recipient informations
     *
     * @param EmailAddress $address
     * @param Option       $name
     */
    public function printTo(EmailAddress $address, Option $name): void;

}
