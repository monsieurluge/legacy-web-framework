<?php

namespace App\Services\Output;

use App\Services\Output\Output;

/**
 * Console Output
 * Immutable object
 */
final class ConsoleOutput implements Output
{

    /**
     * @inheritDoc
     */
    public function write(string $message): Output
    {
        echo $message . "\n";

        return $this;
    }

}
