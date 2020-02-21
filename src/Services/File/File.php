<?php

namespace App\Services\File;

/**
 * A File
 */
interface File
{

    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Returns the path. The path includes the file name.
     *
     * @return string
     */
    public function path(): string;

    /**
     * Writes the file.
     *
     * @return File
     */
    public function write(): File;

}
