<?php

namespace App\Domain\Aggregate;

use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

final class Category
{
    /** @var ID **/
    private $id;
    /** @var Label **/
    private $name;
    /** @var Label **/
    private $parentName;

    /**
     * @param ID    $id
     * @param Label $parentName
     * @param Label $name
     */
    public function __construct(ID $id, Label $parentName, Label $name)
    {
        $this->id         = $id;
        $this->name       = $name;
        $this->parentName = $parentName;
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
     * @return Label
     */
    public function name(): Label
    {
        return empty($this->parentName->value())
            ? $this->name
            : new Label(sprintf('%s > %s', $this->parentName->value(), $this->name->value()));
    }

    /**
     * Factory function which creates a Category using the provided parameters.
     *
     * @param mixed|null  $id
     * @param string|null $parentName
     * @param string|null $name
     *
     * @return Category
     */
    public static function for($id, $parentName, $name): Category
    {
        return new self(
            new ID(is_null($id) ? 0 : intval($id)),
            new Label(is_null($parentName) ? '' : $parentName),
            new Label(is_null($name) ? 'inconnue' : $name)
        );
    }
}
