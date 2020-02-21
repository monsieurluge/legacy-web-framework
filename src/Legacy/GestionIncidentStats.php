<?php

namespace Legacy;

use PDO;
use App\Infrastructure\DataSource\GlobalData;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Templating\TemplateEngine;
use App\Services\Database\DatabaseFactory;
use App\Services\Security\User\User;
use Legacy\GestionIncident;
use Legacy\MaintenanceInterface;

/**
 * GestionIncidentStats
 */
class GestionIncidentStats extends GestionIncident
{

    private $dbFactory;
    private $statsService;

    /**
     * @param TemplateEngine       $templateEngine
     * @param LoggerInterface      $logger
     * @param DatabaseFactory      $dbFactory
     * @param [type]               $service
     * @param MaintenanceInterface $maintenance
     * @param User                 $user
     */
    public function __construct(
        TemplateEngine $templateEngine,
        LoggerInterface $logger,
        $dbFactory,
        $service,
        MaintenanceInterface $maintenance,
        User $user,
        GlobalData $globalData
    ) {
        parent::__construct($templateEngine, $logger, $dbFactory, $maintenance, $user, $globalData);

        $this->dbFactory    = $dbFactory;
        $this->statsService = $service;
    }

    /**
     * TODO [jsonStats description]
     */
    public function jsonStats()
    {
        return json_encode((object) array(
            'cree_par'               => $this->queryDataToGCPie($this->getStatsCreeParCreateur()),
            'en_cours'               => $this->queryDataToGCPie($this->getStatsEnCoursParAssigne()),
            'horaires_creat'         => $this->getStatsHoraires(),
            'temps_moyen'            => $this->getTempsTraitementMoyen(),
            'consacre_moyen'         => $this->getTempsConsacreMoyen(),
            'consacre_moyen_non_nul' => $this->getTempsConsacreMoyenNonNul()
        ));
    }

    /**
     * TODO [csvStatsMain description]
     * @return [type] [description]
     */
    public function csvStatsMain()
    {
        $courbe       = filter_input(INPUT_GET, 'courbe');
        $echelle      = filter_input(INPUT_GET, 'echelle');
        $echelleChamp = filter_input(INPUT_GET, 'ech_champ');
        $donnee       = filter_input(INPUT_GET, 'donnee');
        $date1        = nvl(filter_input(INPUT_GET, 'date1'), '2013-11-25') . ' 00:00:00';
        $date2        = nvl(filter_input(INPUT_GET, 'date2'), date('Y-m-d')) . ' 23:59:59';
        $results      = $this->getStatsMain($courbe, $echelle, $echelleChamp, $donnee, $date1, $date2);
        $data         = $this->statsService->miseEnFormeStatsMain($results, $echelle, $date1, $date2, true);

        header("Content-type: application/vnd.ms-excel");

        header("Content-disposition: attachment; filename=\"export.csv\"");

        //On change le texte de la légende
        $data[0][0] = filter_input(INPUT_GET, 'ech_champ_text') . ' groupé par ' . filter_input(INPUT_GET, 'echelle_text');

        if (empty($data[0][1])) {
            $data[0][1] = filter_input(INPUT_GET, 'donnee_text');
        }

        //On ajoute la ligne d'info
        $libelle = filter_input(INPUT_GET, 'donnee_text') . ' / ' . filter_input(INPUT_GET, 'courbe_text');
        $data    = array_merge(array(array($libelle)), $data);

        return $this->dataToCsv($data);
    }

    /**
     * Convert a data set into a CSV
     * @param array $data
     * @return string
     */
    private function dataToCsv(array $data)
    {
        $out = [];

        foreach($data as $line) {
            $out[] = implode($line, ';');
        }

        return implode($out, "\n");
    }

    /**
     * TODO [jsonStatsMain description]
     */
    public function jsonStatsMain()
    {
        set_time_limit(10);

        $courbe       = filter_input(INPUT_POST, 'courbe');
        $echelle      = filter_input(INPUT_POST, 'echelle');
        $echelleChamp = filter_input(INPUT_POST, 'ech_champ');
        $donnee       = filter_input(INPUT_POST, 'donnee');
        $rawDate1     = filter_input(INPUT_POST, 'date1');
        $rawDate2     = filter_input(INPUT_POST, 'date2');
        $date1        = nvl($rawDate1, '2013-11-25') . ' 00:00:00';
        $date2        = nvl($rawDate2, date('Y-m-d')) . ' 23:59:59';
        $results      = $this->getStatsMain($courbe, $echelle, $echelleChamp, $donnee, $date1, $date2);
        $data         = $this->statsService->miseEnFormeStatsMain($results, $echelle, $date1, $date2);

        return json_encode($data);
    }

    /**
     * TODO [getStatsCreeParCreateur description]
     * @return mixed
     */
    public function getStatsCreeParCreateur()
    {
        $select  = array('b.prenom as label', 'count(*) data');
        $from    = array('incident a join utilisateur b on a.id_createur = b.id');
        $groupBy = array('b.prenom');
        $orderBy = array('2 asc');

        return $this->query($select, $from, array(), $groupBy, $orderBy);
    }

    /**
     * TODO [getStatsEnCoursParAssigne description]
     * @return mixed
     */
    public function getStatsEnCoursParAssigne()
    {
        $select  = array('b.prenom as label', 'count(*) data');
        $from    = array('incident a join utilisateur b on a.id_assigne = b.id');
        $where   = array('date_fermeture is null');
        $groupBy = array('b.prenom');
        $orderBy = array('2 asc');

        return $this->query($select, $from, $where, $groupBy, $orderBy);
    }

    /**
     * TODO [getStatsHoraires description]
     * @return array
     */
    public function getStatsHoraires()
    {
        $select  = array('date_format(date_ouverture, \'%H\') heure', 'count(*) data');
        $from    = array('incident');
        $groupBy = array('date_format(date_ouverture, \'%H\')');
        $orderBy = array('1 asc');
        $data    = $this->query($select, $from, array(), $groupBy, $orderBy);
        $array   = array(array('Horaire', 'Incidents'));

        foreach ($data as $d) {
            $array[] = array($d['heure'], intval($d['data']));
        }

        return $array;
    }

    /**
     * TODO [getTempsTraitementMoyen description]
     * @return int
     */
    public function getTempsTraitementMoyen()
    {
        $select = array('AVG( UNIX_TIMESTAMP( date_fermeture ) - UNIX_TIMESTAMP( date_ouverture ) ) moyenne');
        $from   = array('incident');
        $where  = array('date_fermeture is not null and date_ouverture >= \'' . date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))) . '\'');
        $data   = $this->query($select, $from, $where);

        return round($data[0]['moyenne']);
    }

    /**
     * TODO [getTempsConsacreMoyen description]
     * @return int
     */
    public function getTempsConsacreMoyen()
    {
        $select = array('count(distinct incident.id) nb, sum(incident_note.temps) somme');
        $from   = array('incident join incident_note on incident.id = incident_note.incident_id');
        $where  = array('date_fermeture is not null and date_ouverture >= \'' . date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))) . '\'');
        $data   = $this->query($select, $from, $where);

        return empty($data[0]['nb'])
            ? 0
            : (int) $data[0]['somme'] * 60 / (int) $data[0]['nb'];
    }

    /**
     * TODO [getTempsConsacreMoyenNonNul description]
     * @return int
     */
    public function getTempsConsacreMoyenNonNul()
    {
        $select = array('count(distinct incident.id) nb, sum(incident_note.temps) somme');
        $from   = array('incident join incident_note on incident.id = incident_note.incident_id');
        $where  = array('incident_note.temps > 0 and incident_note.temps is not null and date_fermeture is not null and date_ouverture >= \'' . date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))) . '\'');
        $data   = $this->query($select, $from, $where);

        return empty($data[0]['nb'])
            ? 0
            : (int) $data[0]['somme'] * 60 / (int) $data[0]['nb'];
    }

    /**
     * TODO [query description]
     * @param  array $select
     * @param  [type] $from
     * @param  array  $where
     * @param  array  $groupBy
     * @param  array  $orderBy
     * @param  array  $params
     * @return mixed
     */
    private function query(
        $select,
        $from,
        $where = [],
        $groupBy = [],
        $orderBy = [],
        $params = []
    ) {
        $query = 'select ' . implode($select, ' , ') . ' from ' . implode($from, ' , ');

        if (!empty($where)) {
            $query .= ' where ' . implode($where, ' and ');
        }

        if (!empty($groupBy)) {
            $query .= ' group by ' . implode($groupBy, ' , ');
        }

        if (!empty($orderBy)) {
            $query .= ' order by ' . implode($orderBy, ' , ');
        }

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, $params);
    }

    /**
     * TODO [queryDataToGCPie description]
     * @param  array $data
     * @return array
     */
    private function queryDataToGCPie($data)
    {
        $array = array();

        foreach ($data as $d) {
            $array[] = array($d['label'], intval($d['data']));
        }

        return $array;
    }

}
