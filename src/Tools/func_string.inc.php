<?php

/**
 * Retourne true si str débute par startby
 * @param string $str
 * @param string $startby
 * @return bool
 */
function str_start_by(string $str, string $startby) {
    if (strlen($str) < strlen($startby)) {
        return false;
    } else {
        $debut = substr($str, 0, strlen($startby));

        return $debut == $startby;
    }
}
