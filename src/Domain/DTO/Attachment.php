<?php

namespace App\Domain\DTO;

use App\Services\Text\Text;
use Swift_Message;

interface Attachment
{

    /**
     * Adds the attachment to the message
     *
     * @param Swift_Message $message
     */
    public function attachTo(Swift_Message $message): void;

    /**
     * Returns the name
     *
     * @return Text
     */
    public function name(): Text;

}
