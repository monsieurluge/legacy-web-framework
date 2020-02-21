<?php

namespace App\Services\Security\Storage;

use monsieurluge\result\Result\Result;
use App\Services\Security\Session;
use App\Services\Error\NoValueFailure;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Native Cookie server session.
 */
final class NativeCookie implements Session
{
    /** @var int */
    const TEN_HOURS = 36000;

    /**
     * @inheritDoc
     */
    public function retrieve(string $name): Result
    {
        return isset($_COOKIE[$name])
            ? new Success(
                (object) [ 'value' => $_COOKIE[$name] ]
            )
            : new Failure(
                new NoValueFailure(
                    sprintf(
                        'there is no cookie named "%s"',
                        $name
                    )
                )
            );
    }

    /**
     * @inheritDoc
     */
    public function update(array $values): Session
    {
        foreach ($values as $name => $value) {
            setcookie(
                $name,
                strval($value),
                time() + self::TEN_HOURS
            );
        }

        return $this;
    }
}
