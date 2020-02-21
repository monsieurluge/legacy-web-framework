<?php

namespace App\Services\Request\Constraint;

use monsieurluge\result\Result\Result;
use App\Services\Request\Request;

interface Constraint
{
    /**
     * Validates the request and returns either the request or an Error.
     *
     * @param Request $request
     *
     * @return Result a Result<Request>
     */
    public function validate(Request $request): Result;
}
