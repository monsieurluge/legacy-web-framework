<?php

namespace Legacy;

use \PDO;
use App\Services\Database\DatabaseFactory;

/**
 * DatatableJS
 */
class DatatableJS
{

    /** @var array */
    protected $colsChamps = array();
    /** @var string */
    protected $database;
    /** @var DatabaseFactory */
    protected $dbFactory;
    /** @var string */
    protected $query;
    /** @var mixed */
    protected $queryCount = null;
    /** @var array */
    protected $paramsGet = array();
    /** @var array */
    protected $cols = array();

    /**
     * Creates a DatatableJS object
     * @param string          $database
     * @param string          $query
     * @param DatabaseFactory $dbFactory
     * @param array           $colsChamps
     */
    public function __construct($database, $query, $dbFactory, $colsChamps = array())
    {
        $this->database   = $database;
        $this->dbFactory  = $dbFactory;
        $this->query      = $query;
        $this->colsChamps = $colsChamps;

        $this->setParamsFromGet();
    }

    /**
     * Sets the params using the $_GET data
     */
    protected function setParamsFromGet()
    {
        $this->paramsGet = $_GET;

        foreach ($this->paramsGet as $champ => $valeur) {
            if (str_start_by($champ, 'mDataProp_')) {
                $index = intval(substr($champ, 10));
                $this->cols[$index] = $valeur;
            }
        }
    }

    /**
     * Returns the complete query
     * @return string
     */
    protected function getCompleteQuery()
    {
        $query = $this->query;

        if (isset($this->paramsGet['iSortCol_0']) && $this->paramsGet['iSortCol_0'] !== null) {
            $col = $this->cols[$this->paramsGet['iSortCol_0']];
            $sens = $this->paramsGet['sSortDir_0'];
            $query .= ' order by ' . $col . ' ' . $sens;
        }

        if (isset($this->paramsGet['iDisplayStart'])) {
            $start = $this->paramsGet['iDisplayStart'];
            $length = $this->paramsGet['iDisplayLength'];
            $query .= ' limit ' . $start . ',' . $length;
        }

        return $query;
    }

    /**
     * TODO [setCountQuery description]
     * @param [type] $query [description]
     */
    public function setCountQuery($query)
    {
        $this->queryCount = $query;
    }

    /**
     * TODO [getCountQuery description]
     * @return [type] [description]
     */
    protected function getCountQuery()
    {
        if ($this->queryCount!=null) {
            return $this->queryCount;
        } else {
            return strtr($this->query, array('*' => 'count(*) compteur'));
        }
    }

    /**
     * Returns the results count
     * @return int
     */
    protected function getNbResults()
    {
        $query   = $this->getCountQuery();
        $db      = $this->dbFactory()->createDatabase($this->database, true);
        $results = $db->query($query, PDO::FETCH_ASSOC);

        return $results[0]['compteur'];
    }

    /**
     * Runs the query
     * @return mixed
     */
    protected function execQuery()
    {
        $query = $this->getCompleteQuery();
        $db    = $this->dbFactory()->createDatabase($this->database, true);

        return $db->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the data
     * @return mixed
     */
    protected function getData()
    {
        $results = $this->execQuery();

        return $this->formateResult($results);
    }

    /**
     * Returns the formatted result set
     * @param  array $results
     * @return array
     */
    protected function formateResult($results)
    {
        $formate = array();

        foreach ($results as $l) {
            $temp = array();

            foreach ($this->colsChamps as $cols => $champ) {
                if ($cols == 'DT_RowId') {
                    $temp[$cols] = 'row_' . $l[$champ];
                } else {
                    $temp[$cols] = $l[$champ];
                }
            }

            if ($temp['DT_RowClass'] == 'nouveau') {
                if (isset($lt['depuis']) && $l['depuis']>=1 && $l['depuis']<2) {
                    $temp['DT_RowClass'] .= '_24h';
                } else if(isset($lt['depuis']) && $l['depuis']>=2) {
                    $temp['DT_RowClass'] .= '_48h';
                }
            }

            $formate[] = (object) $temp;
        }

        return $formate;
    }

    /**
     * Returns the result as a JSON
     * @return string
     */
    public function toJson()
    {
        $nbResults = $this->getNbResults();
        $results   = $this->getData();

        $structure = array(
            'iTotalRecords'        => $nbResults,
            'iTotalDisplayRecords' => $nbResults,
            'aaData'               => $results
        );

        return json_encode($structure);
    }

    /**
     * Returns the Database Factory
     * @return DatabaseFactory
     */
    private function dbFactory()
    {
        return $this->dbFactory;
    }

}
