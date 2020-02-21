<?php

namespace App\Domain\ValueObject;

use Exception;

final class EmailAddress implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $email)
    {
        $this->checkEmail(trim($email));

        $this->value = trim($email);
    }

    /**
     * Checks if the given email is valid
     *
     * @param  string $email
     * @throws Exception if the email is invalid
     */
    private function checkEmail(string $email)
    {
        $match = preg_match(
            '#^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$#',
            $email
        );

        if ($match !== 1) {
            throw new Exception(sprintf('the email "%s" seems invalid', $email));
        }
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
