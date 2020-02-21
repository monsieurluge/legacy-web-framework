<?php

namespace App\Domain\DTO;

use App\Domain\DTO\Attachment;
use App\Domain\ValueObject\Label;
use App\Services\Text\Text;
use Swift_ByteStream_FileByteStream;
use Swift_Image;
use Swift_Message;

final class EmbedAttachment implements Attachment
{

    /** @var Text **/
    private $name;
    /** @var Label **/
    private $parameter;
    /** @var Text **/
    private $path;

    /**
     * @param Text  $name
     * @param Text  $path the complete path to the file, including the name
     * @param Label $parameter
     */
    public function __construct(Text $name, Text $path, Label $parameter)
    {
        $this->name      = $name;
        $this->parameter = $parameter;
        $this->path      = $path;
    }

    /**
     * @inheritDoc
     */
    public function attachTo(Swift_Message $message): void
    {
        $cidImage = $message->embed(
            new Swift_Image(
                new Swift_ByteStream_FileByteStream($this->path->toString()),
                $this->name->toString()
            )
        );

        $message->setBody(
            strtr(
                $message->getBody(), // old body
                [ $this->parameter->value() => $cidImage ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function name(): Text
    {
        return $this->name;
    }

}
