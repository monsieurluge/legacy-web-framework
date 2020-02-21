<?php

namespace App\Services\Database;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\DatabaseManager;

/**
 * Manage connexion and query preparation / launched
 */
class Database
{

    /** @var [type] */
    private $_connectionInstances = null;
    /** @var [type] */
    private $_instance            = null;

    /**
     * Constructeur
     * @param string $base base de donnée ("heracles" ou "legacyglobal")
     * @param mixed  $logger
     * @param bool   $utf active la connection utf8
     * @param bool   $persist active la connection persistante
     */
    public function __construct($base, LoggerInterface $logger, $utf = false, $persist = false)
    {
        $this->_connectionInstances =& DatabaseManager::singleton($logger);

        $this->_instance = $this->_connectionInstances->getConnectionInstance($base, $utf, $persist);
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
        return $this->_instance->query($query, $method, $param);
    }

    /**
     * Execute une requête de type INSERT, UPDATE, DELETE
     * Les requête sont preparé par PDO puis stocker pour une ré-execution plus rapide
     * Déconseillé avec une connection persistante
     * @param string $query requête
     * @param array  $param tableau de paramêtre
     * @param bool   $returnLastInsertId retourne le dernier id inserer (ne fonctionne pas en mode buffer)
     * @return int lastinsertID ou null si la requête à eu une erreur, retourne toujours true en mode buffer
     */
    public function exec($query, $param = null, $returnLastInsertId = false)
    {
        return $this->_instance->exec($query, $param, $returnLastInsertId);
    }

    /**
     * Retourne si la connection a la DB a pu ce faire correctement
     * @return bool
     */
    public function connectionEtablished()
    {
        return $this->_instance->connectionEtablished();
    }

    /**
     * Retourne le nombre de ligne affecté par le dernier exec
     * return int
     */
    public function getAffectedRow()
    {
        return $this->_instance->getAffectedRow();
    }

    /**
     * Ouverture du mode de transaction
     * N'est pas supporté par les table MyISAM, si table InnoDB connexion non persistente obligatoire
     * @return bool
     */
    public function begin()
    {
        return $this->_instance->begin();
    }

    /**
     * Execution de la transaction en cours
     * @return bool
     */
    public function commit()
    {
        return $this->_instance->commit();
    }

    /**
     * Annulation de la transaction en cours
     * @return bool
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
        $this->_instance->bufferSet($active);
    }

    /**
     * Retourne l'activation du mode buffer
     * @return bool
     */
    public function bufferGet()
    {
        return $this->_instance->bufferGet();
    }

    /**
     * execute le buffer crée une nouvelle instance pour cela
     * @return bool
     */
    public function bufferFlush()
    {
        return $this->_instance->bufferFlush();
    }

}
