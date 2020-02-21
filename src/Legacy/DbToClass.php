<?php

namespace Legacy;

use \PDO;
use App\Services\Database\DatabaseTools;
use App\Services\Database\DatabaseFactory;

/**
 * DbToClass
 */
abstract class DbToClass
{

    /**
     * TODO get rid of this method
     * GET magic method
     * @param  string $attr
     * @return mixed
     */
    public function __get($attr)
    {
        return $this->$attr;
    }

    /**
     * TODO remove (duplication ?)
     * Cleans a string
     * @param  string $chaine
     * @return string
     */
    private function nettoyeChaine($chaine)
    {
        $chaine = trim(str_replace(array("\r\n", "\r", "\n", PHP_EOL, chr(10), chr(13), chr(10) . chr(13)), " ", $chaine));
        $chaine = preg_replace("/\s+/", " ", $chaine);
        $chaine = strtr($chaine,array(
            "\xEF\x83\xA8\x09" => " "
        ));

        $chaine = preg_replace_callback(
            '/\\\\u([0-9a-fA-F]{4})/',
            function($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
            },
            $chaine
        );

        return $chaine;
    }

    /**
     * TODO [setByArray description]
     * @param array $array
     */
    public function setByArray($array)
    {
        $diff = [];

        foreach ($array as $champ => $valeur) {
            if (property_exists($this, $champ)) {
                if ($this->$champ !== $valeur) {
                    if (isset($array[$champ . '__libelle'])) {
                        $diff[$champ] = ' => ' . substr($this->nettoyeChaine($array[$champ . '__libelle']), 0, 50);
                    } else {
                        $diff[$champ] = substr($this->nettoyeChaine($this->$champ), 0, 50) . ' => ' . substr($this->nettoyeChaine($valeur), 0, 50);
                    }

                    $this->$champ = $valeur;
                }
            }
        }

        return $diff;
    }

    /**
     * TODO [loadByDb description]
     * @param string $database
     * @param string $table
     * @param string $champId
     * @param bool   $utf8
     */
    protected function loadByDb($database, $table, $champId, $utf8 = false)
    {
        $data = DatabaseTools::getTableLine(
            $database,
            $this->logger(),
            $table,
            $champId,
            $this->$champId,
            $utf8
        );

        if (!empty($data)) {
            $this->setByArray($data);
        }
    }

    /**
     * Saves the data to the DB
     * @param  string $database
     * @param  string $table
     * @param  string $champId
     * @param  bool   $utf8
     * @return mixed
     */
    protected function saveInDb($database, $table, $champId, $utf8 = false)
    {
        $data = $this->toArray();

        if (empty($data[$champId])) {
            $issueId = DatabaseTools::insert(
                $database,
                $this->logger(),
                $table,
                $data,
                true,
                $utf8
            );

            if (empty($issueId)) {
                return false;
            }

            $this->$champId = $issueId;

            return true;
        }

        return DatabaseTools::update(
            $database,
            $this->logger(),
            $table,
            $data,
            $champId,
            $data[$champId],
            $utf8
        );
    }

    /**
     * TODO [createByArray description]
     * @param  string $class
     * @param  array $array
     * @return object
     */
    protected static function createByArray($class, $array)
    {
        $objet = new $class();

        $objet->setByArray($array);

        return $objet;
    }

    /**
     * TODO [loadByQuery description]
     * @param  string          $class
     * @param  string          $database
     * @param  string          $query
     * @param  DatabaseFactory $dbFactory
     * @param  array           $params
     * @return array
     */
    protected static function loadByQuery($class, $database, $query, $dbFactory, $params = array())
    {
        $data    = [];
        $results = $dbFactory
            ->createDatabase($database)
            ->query($query, PDO::FETCH_ASSOC, $params);

        foreach ($results as $result) {
            $data[] = self::createByArray($class, $result);
        }

        return $data;
    }

    /**
     * TODO [toArray description]
     * @return array
     */
    public function toArray()
    {
        $data = [];

        foreach ($this as $champ => $valeur) {
            $data[$champ] = $valeur;
        }

        return $data;
    }

    /**
     * TODO [toJson description]
     * @return string
     */
    public function toJson()
    {
        return json_encode((object) $this->toArray());
    }

    /**
     * Returns the logger
     * @return mixed
     */
    abstract protected function logger();

}
