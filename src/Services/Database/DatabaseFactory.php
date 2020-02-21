<?php

namespace App\Services\Database;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\Database;
use App\Services\Database\DatabaseFactoryInterface;

/**
 * Database Factory
 */
final class DatabaseFactory implements DatabaseFactoryInterface
{

    /** @var LoggerInterface */
    private $logger;

    /**
     * Creates the DatabaseFactory object
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Returns the Database instance
     * @param  string $base
     * @param  bool   $utf
     * @param  bool   $persist
     * @return Database
     */
    public function createDatabase($base, $utf = false, $persist = false): Database
    {
        return new Database($base, $this->logger, $utf, $persist);
    }

}
