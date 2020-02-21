<?php

namespace App\Domain\Aggregate;

use App\Domain\ValueObject\Firstname;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Initials;
use App\Domain\ValueObject\Lastname;

/**
 * Agent aggregate.
 */
final class Agent
{
    /** @var ID */
    private $identifier;
    /** @var Initials */
    private $initials;
    /** @var Firstname */
    private $firstname;
    /** @var Lastname */
    private $lastname;

    /**
     * @param ID        $identifier
     * @param Firstname $firstname
     * @param Lastname  $lastname
     * @param Initials  $initials
     */
    public function __construct(ID $identifier, Firstname $firstname, Lastname $lastname, Initials $initials)
    {
        $this->identifier = $identifier;
        $this->initials   = $initials;
        $this->firstname  = $firstname;
        $this->lastname   = $lastname;
    }

    /**
     * Returns the identifier.
     *
     * @return ID
     */
    public function identifier(): ID
    {
        return $this->identifier;
    }

    /**
     * Returns the first name.
     *
     * @return Firstname
     */
    public function firstname(): Firstname
    {
        return $this->firstname;
    }

    /**
     * Returns the initials.
     *
     * @return Initials
     */
    public function initials(): Initials
    {
        return $this->initials;
    }

    /**
     * Returns the last name.
     *
     * @return Lastname
     */
    public function lastname(): Lastname
    {
        return $this->lastname;
    }
}
