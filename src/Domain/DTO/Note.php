<?php

namespace App\Domain\DTO;

use DateTime;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Lastname;
use App\Services\Result\Option;
use App\Services\Text\Text;

/**
 * Note DTO
 */
final class Note
{

    /** @var Text **/
    private $content;
    /** @var DateTime **/
    private $createdAt;
    /** @var DateTime **/
    private $createdBy;
    /** @var Option **/
    private $emailId;
    /** @var Option **/
    private $from;
    /** @var ID **/
    private $identifier;

    /**
     * @param ID       $identifier
     * @param Text     $content
     * @param DateTime $createdAt
     * @param Lastname $createdBy
     * @param Option   $from an optional e-mail sender (Option<Email>)
     * @param Option   $emailId an optional e-mail ID (Option<ID>)
     */
    public function __construct(
        ID $identifier,
        Text $content,
        DateTime $createdAt,
        Lastname $createdBy,
        Option $from,
        Option $emailId
    ) {
        $this->content    = $content;
        $this->createdAt  = $createdAt;
        $this->createdBy  = $createdBy;
        $this->emailId    = $emailId;
        $this->from       = $from;
        $this->identifier = $identifier;
    }

    /**
     * Returns the content
     *
     * @return Text
     */
    public function content(): Text
    {
        return $this->content;
    }

    /**
     * Returns the creation date
     *
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Returns the author last name
     *
     * @return Lastname
     */
    public function createdBy(): Lastname
    {
        return $this->createdBy;
    }

    /**
     * Returns the linked e-mail ID
     *
     * @return Option as Option<ID>
     */
    public function emailId(): Option
    {
        return $this->emailId;
    }

    /**
     * Returns the linked e-mail sender
     *
     * @return Option as Option<Email>
     */
    public function from(): Option
    {
        return $this->from;
    }

    /**
     * Returns the note's ID
     *
     * @return ID
     */
    public function identifier(): ID
    {
        return $this->identifier;
    }

}
