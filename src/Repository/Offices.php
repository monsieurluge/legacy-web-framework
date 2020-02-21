<?php

namespace App\Repository;

use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;

interface Offices
{
    /**
     * Returns the office
     *
     * @param ID $identifier
     *
     * @return Result a Result<office> object
     */
    public function findById(ID $identifier): Result;

    /**
     * Returns the offices for which the ID or the "raison sociale" contains the term.
     *
     * @param  string $term
     * @return array
     */
    public function findByTerm(string $term): array;
}
