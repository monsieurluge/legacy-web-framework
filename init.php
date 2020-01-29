<?php

require_once __DIR__ . '/vendor/autoload.php';

define('PROJECT_ROOT', __DIR__ . '/');

require 'config/config.php';

if (in_array(strtolower(ENVIRONMENT), [ 'local', 'dev' ])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
