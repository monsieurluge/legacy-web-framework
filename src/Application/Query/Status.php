<?php

namespace App\Application\Query;

use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

final class Status
{

    /** @var ID **/
    private $id;
    /** @var Label **/
    private $label;

    public function __construct(ID $id, Label $label)
    {
        $this->id = $id;
        $this->label = $label;
    }

    public function id()
    {
        return $this->id;
    }

    public function label()
    {
        return $this->label;
    }

}
