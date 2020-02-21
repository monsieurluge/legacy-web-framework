<?php

namespace App\Services\Database;

use Exception;
use PDO;
use PDOException;
use App\Services\Database\PreparedQueries;
use App\ServiceInterfaces\Log\LogLevel;
use App\ServiceInterfaces\Log\LoggerInterface;

/**
 * Manage connexion and query preparation / launched
 */
class DatabasePDO
{

    /** @var PDO */
    private $_instance;
    /** @var [type] */
    private $_connexionInformation;
    /** @var [type] */
    private $_utf;
    /** @var [type] */
    private $_persist;
    /** @var [type] */
    private $_bufferActive;
    /** @var [type] */
    private $_bufferQuery;
    /** @var [type] */
    private $_bufferParam;
    /** @var [type] */
    private $_bufferNbQuery;
    /** @var LoggerInterface */
    private $_logger;
    /** @var [type] */
    private $_lastnbrow;
    /** @var [type] */
    private $_connectionOK;
    /** @var PreparedQueries */
    private $_prepareQueries;

    /**
     * Constructeur
     * @param array           $connexionInformation
     * @param LoggerInterface $logger
     * @param bool            $utf mode uf8
     * @param bool            $persist mode persistant
     */
    public function __construct($connexionInformation, LoggerInterface $logger, $utf = false, $persist = false)
    {
        $this->_prepareQueries = PreparedQueries::singleton(serialize($connexionInformation));
        $this->_bufferActive   = false;
        $this->_bufferQuery    = '';
        $this->_bufferNbQuery  = 0;
        $this->_bufferParam    = array();
        $this->_connectionOK   = true;
        $this->_lastnbrow      = 0;
        $this->_logger         = $logger;
        $driverOptions         = array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true);

        if ($persist) {
            $driverOptions[PDO::ATTR_PERSISTENT] = true;
        }

        $this->_connexionInformation = $connexionInformation;
        $this->_utf                  = $utf;
        $this->_persist              = $persist;

        try {
            $this->_instance = new PDO(
                'mysql:dbname=' . $this->_connexionInformation['base'] . ';host=' . $this->_connexionInformation['host'],
                $this->_connexionInformation['user'],
                $this->_connexionInformation['password'],
                $driverOptions
            );

            $this->_instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($utf) {
                $this->_instance->exec("SET CHARACTER SET utf8");
                $this->_instance->exec("SET NAMES utf8");
            }
        } catch (PDOException $exception) {
            $this->_connectionOK = false;

            $this->_logger->log(
                LogLevel::ERROR,
                'Connexion echouee (base = "' . $this->_connexionInformation['base'] .  '") : ' . $exception->getMessage()
            );
        }
    }

    /**
     * TODO [connectionEtablished description]
     * @return [type]
     */
    public function connectionEtablished()
    {
        return $this->_connectionOK;
    }

    /**
     * Execute une requête de type SELECT
     * Les requête sont preparé par PDO puis stocker pour un ré-execution plus rapide
     * @param string $query requête
     * @param int    $method 1=PDO default, 2=PDO::FETCH_ASSOC, 3=PDO::FETCH_NUM
     * @param array  $param tableau de paramêtre
     * @return array
     */
    public function query($query, $method, $param = null)
    {
        $statement = $this->_prepareQueries->checkPrepare($query);

        if ($statement === null) {
            $statement = $this->_instance->prepare($query, [ PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY ]);

            $this->_prepareQueries->addPrepare($query, $statement);
        }

        try {
            if ($param === null) {
                $statement->execute();
            } else {
                $statement->execute($param);
            }

            switch ($method) {
                case 1:
                    $result = $statement->fetchAll();
                    break;
                case 2:
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 3:
                    $result = $statement->fetchAll(PDO::FETCH_NUM);
            }

            return $result;
        } catch (PDOException $exception) {
            $this->_logger->log(
                LogLevel::ERROR,
                sprintf(
                    'error when executing a DB query : %s' . PHP_EOL . 'query = %s' . PHP_EOL . 'parameters = %s',
                    $exception->getMessage(),
                    str_replace("\n", ' ', $query),
                    print_r($param, true)
                )
            );

            throw $exception;
        }
    }

    /**
     * Execute une requête de type INSERT, UPDATE, DELETE
     * Les requête sont preparé par PDO puis stocker pour une ré-execution plus rapide
     * Déconseillé avec une connection persistante
     * @param string $query requête
     * @param array $param tableau de paramêtre
     * @param bool $returnLastInsertId retourne le dernier id inserer (ne fonctionne pas en mode buffer)
     * @return int lastinsertID ou null si la requête à eu une erreur, retourne toujours true en mode buffer
     */
    public function exec($query, $param = null, $returnLastInsertId = false)
    {
        if ($this->_bufferActive === false) {
            try {
                $statement = $this->_prepareQueries->checkPrepare($query);

                if ($statement === null) {
                    $statement = $this->_instance->prepare($query, array(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY));

                    if (strlen($query) < 5000) {
                        $this->_prepareQueries->addPrepare($query, $statement);
                    }
                }

                // execute the statement and fetch the result
                $executionOk = is_null($param) ? $statement->execute() : $statement->execute($param);

                // store the total row nb
                $this->_lastnbrow = $executionOk ? $statement->rowCount() : -1;

                if ($returnLastInsertId) {
                    if ($executionOk) {
                        return $this->_instance->lastInsertId();
                    }
                }

                return $executionOk;
            } catch (PDOException $exception) {
                $debug = print_r($param, true);

                $this->_logger->log(
                    LogLevel::ERROR,
                    str_replace("\n", ' ', $query) . PHP_EOL . $exception->getMessage() . PHP_EOL . $debug
                );

                throw $exception;
            }
        } else {
            $query = preg_replace('#:([a-zA-Z0-9_-]+)#', ':${1}_' . $this->_bufferNbQuery, $query);

            $this->_bufferQuery .= $query . ';';

            foreach ($param as $keyOfParam => $oneParam) {
                $this->_bufferParam[$keyOfParam . '_' . $this->_bufferNbQuery] = $oneParam;
            }

            $this->_bufferNbQuery++;

            if ($this->_bufferNbQuery >= 5000) {
                $this->bufferFlush();
            }

            return true;
        }
    }

    /**
     * Retourne le nombre de ligne affecté par le dernier exec
     * @return int
     */
    public function getAffectedRow()
    {
        return $this->_lastnbrow;
    }

    /**
     * Ouverture du mode de transaction
     * N'est pas supporté par les table MyISAM, si table InnoDB connexion non persistente obligatoire
     * @return [type]
     */
    public function begin()
    {
        return $this->_instance->beginTransaction();
    }

    /**
     * Execution de la transaction en cours
     * @return [type]
     */
    public function commit()
    {
        return $this->_instance->commit();
    }

    /**
     * Annulation de la transaction en cours
     * @return [type]
     */
    public function rollback()
    {
        return $this->_instance->rollback();
    }

    /**
     * Mode buffer, annule l'execution des exec de toutes les instances pdo suivantes (NE PAS UTILISER EN MODE TRANSACTION)
     * permet ainsi l'execution des requête en une seul fois (gain de temps)
     * @param bool $active active ou non le mode, la désactivation n'execute pas le buffer
     */
    public function bufferSet($active = true)
    {
        $this->_bufferActive = $active;
    }

    /**
     * Retourne l'activation du mode buffer
     * @return bool
     */
    public function bufferGet()
    {
        return $this->_bufferActive;
    }

    /**
     * execute le buffer crée une nouvelle instance pour cela
     * @return bool
     */
    public function bufferFlush()
    {
        if ($this->_bufferQuery != '') {
            $instance             = new DatabasePDO($this->_connexionInformation, $this->_logger, $this->_utf, false);
            $return               = $instance->exec($this->_bufferQuery, $this->_bufferParam);
            $this->_bufferQuery   = '';
            $this->_bufferNbQuery = 0;
            $this->_bufferParam   = array();
            $instance             = null;

            return $return;
        }

        return true;
    }

}
