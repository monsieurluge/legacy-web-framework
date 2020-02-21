<?php

namespace App\Services\File;

/**
 * File informations interface.
 */
interface Informations
{

    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Returns the directory.
     *
     * @return string
     */
    public function directory(): string;

    /**
     * Returns the extension.
     *
     * @return string
     */
    public function extension(): string;

}
