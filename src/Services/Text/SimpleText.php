<?php

namespace App\Services\Text;

use App\Services\Text\Text;

final class SimpleText implements Text
{

    /** @var string **/
    private $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->text = $text;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->text;
    }


}
