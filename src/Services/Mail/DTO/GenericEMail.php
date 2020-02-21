<?php

namespace App\Services\Mail\DTO;

use App\Services\Mail\DTO\EMail;

/**
 * Generic E-Mail DTO
 */
final class GenericEMail implements EMail
{

    /** @var array **/
    private $attachments;
    /** @var string **/
    private $body;
    /** @var string **/
    private $from;
    /** @var string **/
    private $subject;
    /** @var array **/
    private $to;
    /** @var int **/
    private $uid;

    public function __construct(
        int $uid,
        string $from,
        string $subject,
        string $body,
        array $attachments = [],
        array $to = []
    ) {
        $this->attachments = $attachments;
        $this->body        = $body;
        $this->from        = $from;
        $this->subject     = $subject;
        $this->to          = $to;
        $this->uid         = $uid;
    }

    /**
     * @inheritDoc
     */
    public function attachments(): array
    {
        return $this->attachments;
    }

    /**
     * @inheritDoc
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function from(): string
    {
        return $this->from;
    }

    /**
     * @inheritDoc
     */
    public function subject(): string
    {
        return $this->subject;
    }

    /**
     * @inheritDoc
     */
    public function to(): array
    {
        return $this->to;
    }

    /**
     * @inheritDoc
     */
    public function uid(): int
    {
        return $this->uid;
    }

}
