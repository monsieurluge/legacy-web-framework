<?php

namespace App\Services\Text;

use App\Services\Text\Text;

/**
 * This object is a clean Windows1252 string
 */
final class Windows1252Text implements Text
{

    /** @var string **/
    private $rawText;

    /**
     * @param string $rawText an UTF8 encoded string
     */
    public function __construct(string $rawText)
    {
        $this->rawText = $rawText;
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return iconv(
            'ISO-8859-1',
            'UTF-8',
            iconv(
                'UTF-8',
                'ISO-8859-1//IGNORE',
                $this->rawText
            )
        );
    }

}
