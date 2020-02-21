<?php

namespace App\Services\Database;

use App\Services\Database\Database;

interface DatabaseFactoryInterface
{

    /**
     * Returns a Database instance
     * @param  string $base
     * @param  bool   $utf
     * @param  bool   $persist
     * @return Database
     */
    public function createDatabase($base, $utf = false, $persist = false): Database;

}
