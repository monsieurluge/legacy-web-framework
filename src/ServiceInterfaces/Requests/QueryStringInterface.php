<?php

namespace App\ServiceInterfaces\Requests;

use \Exception;

/**
 * HTTP Query String interface
 */
interface QueryStringInterface
{

    /**
     * Returns the value for the given parameter, or an Exception if the
     * parameter is not known
     * @param  string $parameter
     * @return string
     * @throws Exception
     */
    public function valueFor(string $parameter): string;

    /**
     * Returns the value for the given parameter, or the default one if the
     *  parameter is not known
     * @param  string $parameter
     * @param  string $default
     * @return string
     */
    public function valueForOr(string $parameter, string $default): string;

}
