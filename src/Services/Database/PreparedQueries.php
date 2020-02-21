<?php

namespace App\Services\Database;

use \PDOStatement;

/**
 * Manager of prepared queries
 */
class PreparedQueries
{

    /** @var array */
    private $data;
    /** @var PreparedQueries */
    private static $_singleton;
    /** @var string */
    private $_usedkey;


    /**
     * Constructeur
     * @param string $key
     * @return PreparedQueries
     */
    static function &singleton($key = 'none')
    {
        if (is_null(self::$_singleton)) {
            self::$_singleton = array();
        }

        if (!isset(self::$_singleton[$key])) {
            self::$_singleton[$key] = new PreparedQueries();
        }

        return self::$_singleton[$key];
    }

    /**
     * Ajoute un PDOStatement selon sa clé
     * @param string $key
     * @param PDOStatement $instance
     */
    public function addPrepare($key, $instance)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $instance;
        }

        $this->addusedkey($key);
    }

    /**
     * Récupère un PDOStatement selon ça clé
     * @param string $key
     * @return PDOStatement (ou null si non trouvé)
     */
    public function checkPrepare($key)
    {
        if (isset($this->data[$key])) {
            $this->updateusedkey($key);

            return $this->data[$key];
        }

        return null;
    }

    /**
     * Creates a PreparedQueries object
     */
    private function __construct()
    {
        $this->data     = array();
        $this->_usedkey = array();
    }

    /**
     * TODO [updateusedkey description]
     * @param  string $key
     * @return [type]
     */
    private function updateusedkey($key)
    {
        // findkey
        $find = false;

        if (count($this->_usedkey) > 0) {
            foreach ($this->_usedkey as $keyofkey => $onkeyused) {
            if ($key == $onkeyused)
                $find = $keyofkey;
            }
        }

        if ($find !== false) {
            unset($this->_usedkey[$find]);

            $this->_usedkey   = array_values($this->_usedkey);
            $this->_usedkey[] = $key;
        }
    }

    /**
     * TODO [addusedkey description]
     * @param  string $key
     */
    private function addusedkey($key)
    {
        $this->_usedkey[] = $key;

        if (count($this->_usedkey) > 40) {
            unset($this->data[$this->_usedkey[0]]);

            array_shift($this->_usedkey);
        }
    }

}
