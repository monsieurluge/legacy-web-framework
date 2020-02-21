<?php

namespace App\Services\Response\JsonApi;

final class Error
{

    /** @var string **/
    private $identifier;
    /** @var string **/
    private $code;
    /** @var string **/
    private $title;

    /**
     * @param string $identifier
     * @param string $code
     * @param string $title
     */
    public function __construct(string $identifier, string $code, string $title)
    {
        $this->identifier = $identifier;
        $this->code       = $code;
        $this->title      = $title;
    }

    /**
     * Converts the Error object to a hash.
     *
     * @return array the hash as follows: [id, code, title]
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->identifier,
            'code'  => $this->code,
            'title' => $this->title
        ];
    }

}
