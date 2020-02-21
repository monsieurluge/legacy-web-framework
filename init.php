<?php

require_once __DIR__ . '/vendor/autoload.php';

define('PROJECT_VERSION', '1.37.0');
define('PROJECT_ROOT', __DIR__ . '/');

require 'config/config.php';
require 'src/Tools/functions.inc.php';

if (in_array(strtolower(ENVIRONMENT), [ 'local', 'dev' ])) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
