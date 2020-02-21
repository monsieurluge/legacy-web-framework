<?php

namespace App\Services\Request;

use \Exception;
use App\ServiceInterfaces\Requests\PathParametersInterface;

/**
 * No Path Parameters
 */
final class NoPathParameters implements PathParametersInterface
{

    /**
     * @inheritDoc
     */
    public function valueFor(string $parameter): string
    {
        throw new Exception('there is no parameter for this path');
    }

}
