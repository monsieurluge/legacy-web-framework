<?php

namespace App\Services\Security\Storage;

use monsieurluge\result\Result\Result;
use App\Services\Security\Session;
use App\Services\Error\NoValueFailure;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Native Server session.
 */
final class NativeSession implements Session
{
    /** @var int */
    const TEN_HOURS = 36000;

    /**
     * constructor
     */
    public function __construct()
    {
        // server should keep session data for AT LEAST 10 hour
        ini_set('session.gc_maxlifetime', self::TEN_HOURS);
        ini_set('session.cookie_lifetime', self::TEN_HOURS);

        // each client should remember their session id for EXACTLY 10 hour
        session_set_cookie_params(self::TEN_HOURS, '/');
    }

    /**
     * @inheritDoc
     */
    public function retrieve(string $key): Result
    {
        session_start();

        $result = isset($_SESSION[$key])
            ? new Success(
                (object) [ 'value' => $_SESSION[$key] ]
            )
            : new Failure(
                new NoValueFailure(
                    sprintf(
                        'no value found in session for the key %s',
                        $key
                    )
                )
            );

        session_write_close();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function update(array $values): Session
    {
        session_start();

        foreach ($values as $key => $value) {
            $_SESSION[$key] = $value;
        }

        session_write_close();

        return $this;
    }
}
