<?php

namespace App\Repository;

use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;

interface SSIIs
{
    /**
     * Returns a Result<array>.
     * The array's keys are the following: [ id, name, addressLine1, addressLine2, addressLine3, postalCode, city, phone, email1, email2, officeId ].
     *
     * @param ID $identifier the SSII's ID
     *
     * @return Result a Result<array>
     */
    public function findById(ID $identifier): Result;

    /**
     * Returns the SSIIs which matches the provided offices.
     *
     * @param array $offices
     *
     * @return array
     */
    public function findByOffices(array $offices): array;

    /**
     * Returns a Result<array>.
     * The array's keys are the following: [ id, name, addressLine1, addressLine2, addressLine3, postalCode, city, phone, email1, email2 ].
     *
     * @param ID $identifier the office's ID
     *
     * @return Result a Result<array>
     */
    public function findByOfficeId(ID $identifier): Result;
}
