<?php

namespace App\Services\Mail;

use Closure;
use App\Domain\ValueObject\EmailAddress;
use App\Services\Mail\Recipient\Main as MainRecipient;
use App\Services\Mail\Recipient\CarbonCopy;

final class RecipientsFromRequest
{

    /** @var array **/
    private $ccPOST;
    /** @var array **/
    private $toPOST;

    public function __construct($toPOST, $ccPOST)
    {
        $this->ccPOST = $ccPOST;
        $this->toPOST = $toPOST;
    }

    /**
     * Returns the recipients
     *
     * @return Recipient[]
     */
    public function create(): array
    {
        return array_merge(
            $this->extractRecipients(
                $this->toPOST,
                function(EmailAddress $emailAddress) { return new MainRecipient($emailAddress); }
            ),
            $this->extractRecipients(
                $this->ccPOST,
                function(EmailAddress $emailAddress) { return new CarbonCopy($emailAddress); }
            )
        );
    }

    /**
     * Extract multiple e-mail adresses from an array of string.
     *
     * @param  array $source an array of adresses like follows: [ 'foo@bar.zx; bar@baz.cv', 'goo@yyy.bn' ]
     * @return array         an array of adresses like follows: [ 'foo@bar.zx', 'bar@baz.cv', 'goo@yyy.bn' ]
     */
    private function extractAdresses(array $source): array
    {
        return array_reduce(
            $source,
            function($accumulator, string $multipleAdresses) {
                return array_merge(
                    $accumulator,
                    explode(';', $multipleAdresses)
                );
            },
            []
        );
    }

    /**
     * Extract the recipients from a given source
     *
     * @param  string[] $from    a list of raw e-mail addresses
     * @param  Closure  $factory f(<EmailAddress>) -> <Recipient>
     * @return Recipient[]
     */
    private function extractRecipients(array $from, Closure $factory): array
    {
        return array_reduce(
            $this->extractAdresses($from),
            function($accumulator, $rawEmailAddress) use ($factory) {
                return array_merge(
                    $accumulator,
                    [ $factory(new EmailAddress($rawEmailAddress)) ]
                );
            },
            []
        );
    }


}
