<?php

namespace App\Services;

final class Stats
{

    /**
     * TODO [miseEnFormeStatsMain description]
     * @param  array   $results
     * @param  string  $echelle
     * @param  [type]  $date1
     * @param  [type]  $date2
     * @param  bool    $datestr
     * @return array
     */
    public function miseEnFormeStatsMain($results, $echelle, $date1, $date2, $datestr = false)
    {
        $data    = array();
        $courbes = array();

        foreach ($results as $r) {
            $k = $r['ech'];

            if (empty($data[$k])) {
                $data[$r['ech']] = array('ech' => $k);
            }

            $courbe = nvl(
                isset($r['label']) ? $r['label'] : '',
                isset($r['courbe']) ? $r['courbe'] : ''
            );

            if (in_array($courbe, $courbes) === false) {
                $courbes[] = $courbe;
            }

            $data[$k][$courbe] = floatval(round($r['valeur'], 2));
        }

        //On ajoute les lignes qu'il manque
        $tmstp1  = date_mysql_to_timestamp($date1);
        $tmstp2  = date_mysql_to_timestamp($date2);
        $mDebut  = 'timestamp_debut_' . $echelle;
        $mFin    = 'timestamp_fin_' . $echelle;
        $courant = $mDebut($tmstp1);
        $max     = $mFin($tmstp2);

        while ($courant < $max) {
            if (empty($data[$courant]) && ($echelle != 'jour' || (date('N', $courant) <= 5 && !publicHoliday($courant)))) {
                $data[$courant] = array('ech' => $courant);
            }

            $courant = $mFin($courant) + 1;
        }

        ksort($data);

        $data2 = array();

        sort($courbes);

        $data2[] = array_merge(array('Echelle'), $courbes);

        foreach ($data as $k => $d) {
            //S'il n'y a pas toutes les courbes à zéro... on les ajoute
            if (count($d) != count($courbes) + 1) {
                foreach ($courbes as $c) {
                    if (empty($data[$k][$c])) {
                        $d[$c] = 0;
                    }
                }
            }

            //On crée à la ligne
            $ar   = array();
            $ar[] = ($datestr == true) ? date('Y-m-d', $d['ech']) : intval($d['ech']);

            foreach ($courbes as $k => $c) {
                $ar[$k + 1] = $d[$c];
            }

            //On ajoute la ligne
            $data2[] = $ar;
        }

        return $data2;
    }

}
