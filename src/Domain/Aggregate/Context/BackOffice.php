<?php

namespace App\Domain\Aggregate\Context;

use App\Domain\Aggregate\Context\Context;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

/**
 * Back Office
 */
final class BackOffice implements Context
{
    /** @var ID */
    private $identifier;
    /** @var Label */
    private $label;

    /**
     * Creates the context
     */
    public function __construct()
    {
        $this->identifier = new ID(3);
        $this->label      = new Label('BACK OFFICE');
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
