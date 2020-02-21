<?php

namespace App\Domain\DTO;

use DateTime;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

final class FileDescription
{

    /** @var ID **/
    private $identifier;
    /** @var ID **/
    private $issueId;
    /** @var Label **/
    private $name;
    /** @var DateTime **/
    private $createdAt;
    /** @var ID **/
    private $createdBy;

    /**
     * @param ID       $identifier
     * @param Label    $name
     * @param DateTime $createdAt
     * @param ID       $createdBy
     * @param ID       $issueId
     */
    public function __construct(ID $identifier, Label $name, DateTime $createdAt, ID $createdBy, ID $issueId)
    {
        $this->createdAt  = $createdAt;
        $this->createdBy  = $createdBy;
        $this->identifier = $identifier;
        $this->issueId    = $issueId;
        $this->name       = $name;
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
     * Returns the user's ID which added this file
     *
     * @return ID
     */
    public function createdBy(): ID
    {
        return $this->createdBy;
    }

    /**
     * Returns the file's ID
     *
     * @return ID
     */
    public function identifier(): ID
    {
        return $this->identifier;
    }

    /**
     * Returns the linked issue's ID
     *
     * @return ID
     */
    public function issueId(): ID
    {
        return $this->issueId;
    }

    /**
     * Returns the file's name
     *
     * @return Label
     */
    public function name(): Label
    {
        return $this->name;
    }

}
