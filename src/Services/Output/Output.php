<?php

namespace App\Services\Output;

/**
 * Output interface
 */
interface Output
{

    /**
     * Writes the message
     * @param string $message
     * @return Output
     */
    public function write(string $message): Output;

}
