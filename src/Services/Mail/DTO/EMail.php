<?php

namespace App\Services\Mail\DTO;

/**
 * E-Mail DTO interface
 */
interface EMail
{

    /**
     * Returns the attachments
     * @return array
     */
    public function attachments(): array;

    /**
     * Returns the body (UTF-8 encoded)
     * @return string
     */
    public function body(): string;

    /**
     * Returns the "from" e-mail address
     * @return string
     */
    public function from(): string;

    /**
     * Returns the subject (UTF-8 encoded)
     * @return string
     */
    public function subject(): string;

    /**
     * Returns the "to" e-mail addresses
     * @return string[]
     */
    public function to(): array;

    /**
     * Returns the UID
     * @return int
     */
    public function uid(): int;

}
