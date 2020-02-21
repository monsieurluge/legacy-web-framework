<?php

namespace App\Services\Security;

use monsieurluge\result\Result\Result;

/**
 * Server Session interface.
 */
interface Session
{
    /**
     * Returns the value stored with the given key, if found
     *
     * @param string $key
     *
     * @return Result
     */
    public function retrieve(string $key): Result;

    /**
     * Update the given key-value pairs
     *
     * @param array $values
     *
     * @return Session
     */
    public function update(array $values): Session;
}
