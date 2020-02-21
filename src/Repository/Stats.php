<?php

namespace App\Repository;

use PDO;

final class Stats
{

    private $dbFactory;

    public function __construct($dbFactory)
    {
        $this->dbFactory = $dbFactory;
    }

    /**
     * TODO [findFromFilters description]
     *
     * @param  array $filters
     * @return array
     */
    public function findFromFilters(array $filters = []): array
    {
        $select  = [];
        $from    = [];
        $where   = [];
        $groupBy = [];

        $courbe       = $filters['courbe'];
        $echelle      = $filters['echelle'];
        $echelleChamp = $filters['ech_champ'];
        $donnee       = $filters['donnee'];
        $date1        = $filters['date1'];
        $date2        = $filters['date2'];

        switch ($echelle) {
            case 'jour':
                $select[] = 'UNIX_TIMESTAMP(DATE_FORMAT(' . $echelleChamp . ',\'%Y-%m-%d\')) ech';
                $where[] = $echelleChamp . ' between \'' . $date1 . '\' and \'' . $date2 . '\'';
                $groupBy[] = 'DATE_FORMAT(' . $echelleChamp . ',\'%Y-%m-%d\')';
                break;
            case 'semaine':
                $select[] = 'UNIX_TIMESTAMP(DATE_FORMAT(DATE_SUB(' . $echelleChamp . ',INTERVAL DAYOFWEEK( ' . $echelleChamp . ' ) - 2 DAY),\'%Y-%m-%d\')) ech';
                $where[] = $echelleChamp . ' between \'' . $date1 . '\' and \'' . $date2 . '\'';
                $groupBy[] = 'DATE_FORMAT(DATE_SUB(' . $echelleChamp . ',INTERVAL DAYOFWEEK( ' . $echelleChamp . ' ) - 2 DAY),\'%Y-%m-%d\')';
                break;
            case 'mois':
                $select[] = 'UNIX_TIMESTAMP(DATE_FORMAT(' . $echelleChamp . ',\'%Y-%m-01\')) ech';
                $where[] = $echelleChamp . ' between \'' . $date1 . '\' and \'' . $date2 . '\'';
                $groupBy[] = 'DATE_FORMAT(' . $echelleChamp . ',\'%Y-%m-01\')';
                break;
            case 'annee':
                $select[] = 'UNIX_TIMESTAMP(DATE_FORMAT(' . $echelleChamp . ',\'%Y-01-01\')) ech';
                $where[] = $echelleChamp . ' between \'' . $date1 . '\' and \'' . $date2 . '\'';
                $groupBy[] = 'DATE_FORMAT(' . $echelleChamp . ',\'%Y-01-01\')';
                break;
        }

        $where[] = $echelleChamp . ' is not null';

        switch ($donnee) {
            case 'nb_incident':
                $select[] = 'count(*) valeur';
                break;
            case 'nb_escalade':
                $select[] = 'count(*) valeur';
                $where[] = 'escalade > 0';
                break;
            case 'temps':
                $select[] = 'avg(unix_timestamp(date_fermeture)-unix_timestamp(date_ouverture))/3600 valeur';
                $where[] = 'date_ouverture is not null';
                $where[] = 'date_fermeture is not null';
                break;
            case 'temps_ouvre':
                $select[] = 'date_ouverture,date_fermeture';
                $where = array(
                    'date_ouverture is not null',
                    'date_fermeture is not null'
                );
                break;
            case 'duree_moyen':
                $select[] = 'round((sum(incident_note.temps)/count(distinct incident.id)),2) valeur';
                $from[] = 'incident_note';
                $where[] = 'incident.id = incident_note.incident_id';
                $where[] = 'incident.date_ouverture is not null';
                $where[] = 'incident.date_fermeture is not null';
                break;
            case 'duree_cumule':
                $select[] = 'round(sum(incident_note.temps)/60,2) valeur';
                $from[] = 'incident_note';
                $where[] = 'incident.id = incident_note.incident_id';
                $where[] = 'incident.date_ouverture is not null';
                $where[] = 'incident.date_fermeture is not null';
                break;
        }

        switch ($courbe) {
            case 'tout':
                $from[] = 'incident';
                break;
            case 'id_createur':
                $select[] = 'b.prenom courbe';
                $from[] = 'incident join utilisateur b on incident.id_createur = b.id';
                $groupBy[] = 'id_createur';
                break;
            case 'id_assigne':
                $select[] = 'b.prenom courbe';
                $from[] = 'incident join utilisateur b on incident.id_assigne = b.id';
                $groupBy[] = 'id_assigne';
                break;
            case 'id_ssii':
                $select[] = 'id_ssii courbe';
                $from[] = 'incident';
                $where[] = 'id_ssii is not null';
                $groupBy[] = 'id_ssii';
                $labels = $this->getSsii();
                break;
            case 'priorite';
                $select[] = 'incident.priorite courbe, priorite.libelle label';
                $from[] = 'incident join priorite on incident.priorite = priorite.id';
                $groupBy[] = 'incident.priorite';
                break;
            case 'origine';
                $select[] = 'incident.origine courbe';
                $from[] = 'incident';
                $groupBy[] = 'incident.origine';
                break;
            case 'type';
                $select[] = 'incident.type courbe';
                $from[] = 'incident';
                $groupBy[] = 'incident.type';
                break;
            case 'categorie':
                $colonne = 'ifnull(e.nom,ifnull(d.nom,ifnull(c.nom,b.nom)))';
                $select[] = $colonne . ' courbe';
                $from[] = 'incident'
                    . ' join categorie b'
                    . ' on incident.categorie = b.id'
                    . ' left join categorie c'
                    . ' on b.parent = c.id'
                    . ' left join categorie d'
                    . ' on c.parent = d.id'
                    . ' left join categorie e'
                    . ' on d.parent = e.id';
                $groupBy[] = $colonne;
                break;
        }

        if ($donnee == 'temps_ouvre') {
            $groupBy = array();
        }

        $results = $this->query($select, $from, $where, $groupBy);

        if ($donnee == 'temps_ouvre') {
            $groupBy = array();
            $rs2     = array();

            foreach ($results as $r) {
                $c = nvl($r['courbe'],0);

                if (empty($rs2[$r['ech']][$c])) {
                    $rs2[$r['ech']][$c] = array('nb' => 0, 'temps' => '');
                }

                $rs2[$r['ech']][$c]['nb'] ++;
                $rs2[$r['ech']][$c]['temps'] += calcTempsOuvre($r['date_ouverture'], $r['date_fermeture']);
            }

            $rs3 = array();

            foreach ($rs2 as $ech => $val1) {
                foreach ($val1 as $courbe => $val2) {
                    $rs3[] = array(
                        'ech'    => $ech,
                        'courbe' => $courbe,
                        'valeur' => ($val2['temps'] / 3600) / $val2['nb']
                    );
                }
            }

            $results = $rs3;
        }

        if (!empty($labels)) {
            foreach ($results as $k => $r) {
                $results[$k]['courbe'] = $labels[$r['courbe']];
            }
        }

        return $results;
    }

    /**
     * TODO [getSsii description]
     * @return array
     */
    private function getSsii()
    {
        $results = $this->dbFactory
            ->createDatabase(DB_SSII['base'])
            ->query('select * from ssii', PDO::FETCH_ASSOC);

        $data = array();

        foreach ($results as $r) {
            $data[$r['id_ssii']] = $r['raison_sociale'];
        }

        return $data;
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
    private function query($select, $from, $where = array(), $groupBy = array(), $orderBy = array(), $params = array())
    {
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

}
