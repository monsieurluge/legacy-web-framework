<?php

namespace App\Services\Request\Constraint;

use App\Core\Exceptions\NotAllowedException;
use monsieurluge\result\Error\BaseError;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Services\Request\Constraint\Constraint;
use App\Services\Request\Request;

/**
 * User Logged constraint
 */
final class Logged implements Constraint
{
    /**
     * @inheritDoc
     */
    public function validate(Request $request): Result
    {
        if (false === $request->user()->logged()) {
            throw new NotAllowedException();
        }

        return $request->user()->logged()
            ? new Success($request)
            : new Failure(new BaseError('tst-1', 'l\'utilisateur doit être connecté'));
    }
}
