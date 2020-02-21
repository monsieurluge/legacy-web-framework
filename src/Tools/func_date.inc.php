<?php

date_default_timezone_set('Europe/Paris');

/**
 * TODO [date_mysql_to_timestamp description]
 * @param  [type] $datetime
 * @return [type]
 */
function date_mysql_to_timestamp($datetime)
{
    if (strlen($datetime) > 10) {
        list($date, $heure) = explode(' ', $datetime);
        list($a, $m, $j) = explode('-', $date);
        list($h, $i, $s) = explode(':', $heure);

        return mktime($h, $i, $s, $m, $j, $a);
    } else {
        list($a, $m, $j) = explode('-', $datetime);

        return mktime(0, 0, 0, $m, $j, $a);
    }
}

function timestamp_debut_annee($t) {
    return mktime(0, 0, 0, 1, 1, date('Y', $t));
}

function timestamp_fin_annee($t) {
    return mktime(23, 59, 59, 12, 31, date('Y', $t));
}

function timestamp_debut_mois($t) {
    return mktime(0, 0, 0, date('m', $t), 1, date('Y', $t));
}

function timestamp_fin_mois($t) {
    return mktime(23, 59, 59, date('m', $t), date('t', $t), date('Y', $t));
}

function timestamp_debut_semaine($t) {
    $j = date('d', $t) - (date('N', $t) - 1);
    return mktime(0, 0, 0, date('m', $t), $j, date('Y', $t));
}

function timestamp_fin_semaine($t) {
    $j = (date('d', $t) - (date('N', $t) - 1)) + 6;
    return mktime(23, 59, 59, date('m', $t), $j, date('Y', $t));
}

function timestamp_debut_jour($t) {
    return mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
}

function timestamp_fin_jour($t) {
    return mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));
}

/**
 * TODO [calcTempsOuvre description]
 * @param  string $date1
 * @param  string $date2
 * @return int Temps ouvré en seconde
 */
function calcTempsOuvre($date1, $date2)
{
    $d1 = date_mysql_to_timestamp($date1);

    if (empty($date2)) {
        $d2 = time();
    } else {
        $d2 = date_mysql_to_timestamp($date2);
    }

    $tps = 0;

    if (intval(date('H', $d1)) < 9) {
        $d1 = mktime(9, 0, 0, date('m', $d1), date('d', $d1), date('Y', $d1));
    }

    if (date('H:i', $d2) > '17:30') {
        $d2 = mktime(17, 30, 0, date('m', $d2), date('d', $d2), date('Y', $d2));
    }

    if ($d2 < $d1) {
        return 0;
    }

    while ($d1 < $d2 && $d1 < time()) {
        $fin = mktime(17, 30, 0, date('m', $d1), date('d', $d1), date('Y', $d1));

        if (date('Y-m-d', $d1) == date('Y-m-d',$d2)) {
            $tps += $d2 - $d1;
            break;
        } else {
            $tps += $fin - $d1;

            $d1 = mktime(9, 0, 0, date('m', $d1), date('d', $d1) + 1, date('Y', $d1));
        }
    }

    return $tps;
}

/**
 * TODO [publicHoliday description]
 * @param [type] $date
 * @return [type]
 */
function publicHoliday($date)
{
    $easterDate = easter_date(intval(date('Y', $date)));
    $easterDay = date('j', $easterDate);
    $easterMonth = date('n', $easterDate);
    $easterYear = date('Y', $easterDate);

    $lundiPaques = mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear); // Lundi de paques
    $ascension = mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear); // Ascension
    $pentecote = mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear); // Pentecote

    $tab_ferie = array(
        '01/01',
        '01/05',
        '08/05',
        '14/07',
        '15/08',
        '01/11',
        '11/11',
        '25/12',
        date('d/m', $lundiPaques),
        date('d/m', $ascension),
        date('d/m', $pentecote)
    );

    if (in_array(date('d/m', $date), $tab_ferie)) {
        return true;
    } else {
        return false;
    }
}
