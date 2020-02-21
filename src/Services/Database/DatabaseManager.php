<?php

namespace App\Services\Database;

use \Exception;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabasePDO;

/**
 * Manager of database connexion instance
 */
class DatabaseManager
{

    /** @var array */
    private $data;
    /** @var LoggerInterface */
    private $logger;
    /** @var DatabaseManager */
    private static $_singleton;

    /**
     * Singleton
     * @param mixed $logger
     * @return DatabaseManager
     */
    static function &singleton(LoggerInterface $logger)
    {
        if (is_null(self::$_singleton)) {
            self::$_singleton = new DatabaseManager($logger);
        }

        return self::$_singleton;
    }

    /**
     * @param LoggerInterface $logger
     */
    private function __construct($logger)
    {
        $this->data   = array();
        $this->logger = $logger;
    }

    /**
     * Récupère une instance de connection
     * @param string $base base de données
     * @param bool   $utf mode utf8
     * @param bool   $persist mode persitant
     * @return DatabasePDO
     */
    public function getConnectionInstance($base, $utf, $persist)
    {
        if (!isset($this->data[$base][$utf][$persist])) {
            $this->data[$base][$utf][$persist] = $this->createConnexionInstance($base, $utf, $persist);
        }

        return $this->data[$base][$utf][$persist];
    }

    /**
     * TODO [createConnexionInstance description]
     * @param  string $base      [description]
     * @param  bool   $utf       [description]
     * @param  bool   $persist   [description]
     * @return DatabasePDO
     * @throws Exception if the database name is not known
     */
    private function createConnexionInstance($base, $utf, $persist)
    {
        switch($base) {
            case DB_INCIDENT['base']:
                $connexionInformation = DB_INCIDENT;
                break;
            case DB_GLOBAL['base']:
                $connexionInformation = DB_GLOBAL;
                break;
            case DB_SSII['base']:
                $connexionInformation = DB_SSII;
                break;
            default:
                throw new Exception(sprintf('la base %s est inconnue', $base));
                break;
        }

        return new DatabasePDO($connexionInformation, $this->logger, $utf, $persist);
    }

}
