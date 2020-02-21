<?php

namespace App\Domain\DTO;

use App\Domain\DTO\Attachment;
use App\Services\Text\Text;

final class Email
{

    /** @var Text **/
    private $subject;
    /** @var Text **/
    private $body;
    /** @var Attachment[] **/
    private $attachments;

    /**
     * @param Text         $subject
     * @param Text         $body
     * @param Attachment[] $attachments
     */
    public function __construct(Text $subject, Text $body, array $attachments)
    {
        $this->attachments = $attachments;
        $this->body        = $body;
        $this->subject     = $subject;
    }

    /**
     * Returns the attachments
     *
     * @return Attachment[]
     */
    public function attachments(): array
    {
        return $this->attachments;

    }

    /**
     * Returns the body
     *
     * @return Text
     */
    public function body(): Text
    {
        return $this->body;
    }

    /**
     * Returns the subject
     *
     * @return Text
     */
    public function subject(): Text
    {
        return $this->subject;
    }

}
