<?php

namespace App\Domain\DTO;

use Exception;
use App\Domain\DTO\Attachment;
use App\Services\Text\Text;
use Swift_Attachment;
use Swift_ByteStream_FileByteStream;
use Swift_Message;

final class FileAttachment implements Attachment
{

    /** @var Text **/
    private $name;
    /** @var Text **/
    private $path;

    /**
     * @param Text $name
     * @param Text $path
     */
    public function __construct(Text $name, Text $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function attachTo(Swift_Message $message): void
    {
        if (false === file_exists($this->path->toString())) {
            throw new Exception(sprintf(
                'cannot attach the file "%s": not found',
                $this->name->toString()
            ));
        }

        $message->attach(
            new Swift_Attachment(
                new Swift_ByteStream_FileByteStream($this->path->toString()),
                $this->name->toString()
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
