<?php

namespace App\Services\Database;

use Exception;
use PDO;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Database\Database;

/**
 * DatabaseTools
 */
class DatabaseTools
{

    /** @var bool */
    public static $UTF8 = true;

    /**
     * Retourne une liste d'id
     * @param string          $database Nom de la base
     * @param LoggerInterface $logger
     * @param string          $table Table ou lire la liste
     * @param string          $id Champ à retourner
     * @param string          $champCle Champ clé qui servira de where
     * @param string          $valeurCle Valeur clé qui servira de where
     * @return string[]
     */
    public static function getListId($database, LoggerInterface $logger, $table, $id, $champCle = '', $valeurCle = '', $utf8 = false)
    {
        if (!empty($champCle)) {
            $query  = 'select ' . $id . ' from ' . $table . ' where ' . $champCle . ' = :cle';
            $params = array('cle' => $valeurCle);
        } else {
            $query  = 'select ' . $id . ' from ' . $table;
            $params = array();
        }

        $database = new Database($database, $logger, $utf8);
        $results  = $database->query($query, PDO::FETCH_ASSOC, $params);
        $retour   = array();

        foreach ($results as $result) {
            $retour[] = $result[$id];
        }

        return $retour;
    }

    /**
     * Retourne un tableau de données correspondant à plusieurs lignes d'une table
     * @param string          $database Nom de la base de données
     * @param LoggerInterface $logger
     * @param string          $table Nom sql de la table
     * @param string          $champCle Champ clé pour la recherche
     * @param string          $valeurCle Valeur de clé
     * @return string[][]
     * @throws Exception
     */
    public static function getTableLines($database, LoggerInterface $logger, $table, $champCle, $valeurCle, $utf8 = false)
    {
        $query    = 'select * from ' . $table . ' where ' . $champCle . ' = :cle';
        $database = new Database($database, $logger, $utf8);
        $results  = $database->query($query, PDO::FETCH_ASSOC, array('cle' => $valeurCle));

        return $results;
    }

    /**
     * Retourne un tableau de données correspondant à une ligne d'une table
     * @param string          $database Nom de la base de données
     * @param LoggerInterface $logger
     * @param string          $table Nom sql de la table
     * @param string          $champCle Champ clé pour la recherche
     * @param string          $valeurCle Valeur de clé
     * @return string[]
     * @throws Exception
     */
    public static function getTableLine($database, LoggerInterface $logger, $table, $champCle, $valeurCle, $utf8 = false)
    {
        $resultats = self::getTableLines($database, $logger, $table, $champCle, $valeurCle, $utf8);

        if (count($resultats) > 1) {
            throw new Exception($database . '/' . $table . ' clé ' . $champCle . '=' . $valeurCle . ' : Recherche retournant plus d\'un resultat');
        } else if (count($resultats) == 0) {
            return null;
        } else {
            return $resultats[0];
        }
    }

    /**
     * TODO [nettoyeChamp description]
     * @param  string $champ
     * @return string
     */
    protected static function nettoyeChamp($champ)
    {
        $champ = strtolower($champ);
        $champ = strtr(
            $champ,
            array(
                '_' => '',
                '-' => ''
            )
        );

        return $champ;
    }

    /**
     * TODO [implodeDataToQuery description]
     * @param  string $data
     * @return string
     */
    protected static function implodeDataToQuery($data)
    {
        $tabString = array();

        foreach ($data as $champ => $valeur) {
            $tabString[] = '`' . $champ . '` = :' . self::nettoyeChamp($champ);
        }

        return implode($tabString, ' , ');
    }

    /**
     * TODO [implodeDataToParams description]
     * @param  array $data
     * @return array
     */
    protected static function implodeDataToParams($data) {
        $params = array();

        foreach ($data as $champ => $valeur) {
            if (str_start_by($champ, 'id_') && empty($valeur)) {
                $params[self::nettoyeChamp($champ)] = null;
            } else {
                $params[self::nettoyeChamp($champ)] = $valeur;
            }
        }

        return $params;
    }

    /**
     * TODO [insert description]
     * @param string          $database
     * @param LoggerInterface $logger
     * @param string          $table
     * @param string[]        $data
     * @param bool            $saveEmpty
     * @return mixed, ENT_QUOTES
     */
    public static function insert($database, LoggerInterface $logger, $table, $data, $saveEmpty = false, $utf8 = false)
    {
        $donnees = $valeurs = array();

        foreach ($data as $champ => $valeur) {
            if ($valeur !== null && ($valeur !== '' || $saveEmpty)) {
                $valeurs[]       = '`'.$champ.'`' . ' = :' . $champ;
                $donnees[$champ] = $valeur;
            }
        }

        $query    = 'insert into ' . $table . ' set ' . implode($valeurs, ' , ');
        $database = new Database($database, $logger, $utf8);

        return $database->exec($query, $donnees, true);
    }

    /**
     * TODO [update description]
     * @param string          $database
     * @param LoggerInterface $logger
     * @param string          $table
     * @param string[]        $data
     * @param string          $champCle
     * @param string          $valeurCle
     * @return mixed
     */
    public static function update($database, LoggerInterface $logger, $table, $data, $champCle, $valeurCle, $utf8 = false)
    {
        $query         = 'update ' . $table . ' set ' . self::implodeDataToQuery($data) . ' where ' . $champCle . ' = :cle';
        $params        = self::implodeDataToParams($data);
        $params['cle'] = $valeurCle;
        $database      = new Database($database, $logger, $utf8);

        return $database->exec($query, $params);
    }

    /**
     * TODO [merge description]
     * @param string          $database
     * @param LoggerInterface $logger
     * @param string          $table
     * @param string[]        $data
     * @param string          $champCle
     * @param string          $valeurCle
     * @return mixed
     */
    public static function merge($database, LoggerInterface $logger, $table, $data, $champCle, $valeurCle)
    {
        $ligne = self::getTableLine($database, $logger, $table, $champCle, $valeurCle);

        if (empty($ligne)) {
            return self::insert($database, $logger, $table, $data);
        } else {
            self::update($database, $logger, $table, $data, $champCle, $valeurCle);
        }

        return null;
    }

    /**
     * TODO [replace description]
     * @param string          $database
     * @param LoggerInterface $logger
     * @param string          $table
     * @param string[]        $data
     * @return mixed
     */
    public static function replace($database, LoggerInterface $logger, $table, $data, $utf8 = false)
    {
        $query    = 'replace into ' . $table . ' set ' . self::implodeDataToQuery($data);
        $database = new Database($database, $logger, $utf8);

        return $database->exec($query, self::implodeDataToParams($data), true);
    }

    /**
     * TODO [delete description]
     * @param string          $database
     * @param LoggerInterface $logger
     * @param string          $table
     * @param string          $champCle
     * @param string          $valeurCle
     */
    public static function delete($database, LoggerInterface $logger, $table, $champCle, $valeurCle, $utf8 = false)
    {
        $query    = 'delete from ' . $table . ' where ' . $champCle . ' = :cle';
        $database = new Database($database, $logger, $utf8);

        return $database->exec($query, array('cle' => $valeurCle));
    }

}
