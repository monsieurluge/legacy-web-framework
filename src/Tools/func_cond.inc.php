<?php

/**
 * Si val1 n'est pas empty, retourne val1, sinon retourne val2
 * @param [type] $val1
 * @param [type] $val2
 * @return mixed
 */
function nvl($val1, $val2) {
    return (!empty($val1)) ? $val1 : $val2;
}

/**
 * Si cond n'est pas empty, retourne val1 sinon retourne val2
 * @param [type] $cond
 * @param [type] $val1
 * @param [type] $val2
 * @return mixed
 */
function nvl2($cond, $val1, $val2) {
    return (!empty($cond)) ? $val1 : $val2;
}
