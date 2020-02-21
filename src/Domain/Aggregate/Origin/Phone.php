<?php

namespace App\Domain\Aggregate\Origin;

use App\Domain\Aggregate\Origin\Origin;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

/**
 * Phone.
 */
final class Phone implements Origin
{
    /** @var ID */
    private $identifier;
    /** @var Label */
    private $label;

    /**
     * Creates the origin.
     */
    public function __construct()
    {
        $this->identifier = new ID(1);
        $this->label      = new Label('tel');
    }

    /**
     * @inheritDoc
     */
    public function identifier(): ID
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function label(): Label
    {
        return $this->label;
    }
}
