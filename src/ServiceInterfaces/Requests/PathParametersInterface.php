<?php

namespace App\ServiceInterfaces\Requests;

use \Exception;

/**
 * HTTP Request Path Parameters Interface
 */
interface PathParametersInterface
{

    /**
     * Returns the value for the given parameter
     *
     * @param  string $parameter
     * @return string
     * @throws Exception if the parameter is not part of the HTTP Request path
     */
    public function valueFor(string $parameter): string;

}
