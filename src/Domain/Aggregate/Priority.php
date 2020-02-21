<?php

namespace App\Domain\Aggregate;

use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

final class Priority
{

    /** @var ID **/
    private $id;
    /** @var Label **/
    private $name;

    /**
     * @param ID    $id
     * @param Label $name
     */
    public function __construct(ID $id, Label $name)
    {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * Returns the ID.
     *
     * @return ID
     */
    public function id(): ID
    {
        return $this->id;
    }

    /**
     * Returns the name.
     *
     * @return name
     */
    public function name(): Label
    {
        return $this->name;
    }

}
