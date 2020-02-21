<?php

namespace App\Services\Mail\Recipient;

use App\Services\Mail\Builder\MailBuilder;
use App\Services\Printer\RecipientPrinter;

interface Recipient
{

    /**
     * Adds the recipient to the given mail builder
     *
     * @param MailBuilder $builder
     */
    public function addTo(MailBuilder $builder): void;

    /**
     * Prints the recipient informations using a dedicated printer
     *
     * @param RecipientPrinter $printer
     */
    public function print(RecipientPrinter $printer): void;

}
