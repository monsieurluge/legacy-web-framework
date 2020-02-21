<?php

 // PROJECT
define('UTF8', true);
define('PROJET', 'INCIDENTS');
define('ENVIRONMENT', 'sample');

// DATABASE
define('DB_SSII', [
    'base'     => 'base_name',
    'host'     => '000.000.000.000',
    'user'     => 'db_user',
    'password' => 'db_password'
]);
define('DB_INCIDENT', [
    'base'     => 'base_name',
    'host'     => '000.000.000.000',
    'user'     => 'db_user',
    'password' => 'db_password'
]);
define('DB_GLOBAL', [
    'base'     => 'base_name',
    'host'     => '000.000.000.000',
    'user'     => 'db_user',
    'password' => 'db_password'
]);

// FOLDER
define('DOSSIER_TPL', PROJECT_ROOT . 'tpl/');
define('DOSSIER_TPL_C', PROJECT_ROOT . 'templates_c/');
define('DOSSIER_TPL_IMAGES', PROJECT_ROOT . 'public/images/');
define('DOSSIER_UPLOAD', PROJECT_ROOT . 'upload/files/');
define('LOGS_FOLDER', PROJECT_ROOT . 'logs/');

// MAIL
define('MAIL_IMAP', 'host');
define('MAIL_IMAP_PORT', 123);
define('MAIL_IMAP_FOLDER', 'inbox folder');
define('MAIL_IMAP_ERROR_FOLDER', 'inbox/errors folder');
define('MAIL_IMAP_PROCESSED_FOLDER', 'inbox/traites folder');
define('MAIL_IMAP_SECURITY', 'security');
define('MAIL_IMAP_USER', 'user');
define('MAIL_IMAP_PASSWORD', 'password');

define('MAIL_SMTP', 'host');
define('MAIL_SMTP_PORT', 123);
define('MAIL_SMTP_SECURITY', 'security');
define('MAIL_SMTP_USER', 'user');
define('MAIL_SMTP_PASSWORD', 'password');

define('MAIL_SENDER_NAME', 'Toto');
define('MAIL_SENDER_EMAIL', 'toto@nope.com');

define('MAIL_DEBUG', 'mail@debug.com');
define('MAIL_ERREUR', 'mail@error.com');

define('MAIL_BLACKLIST', [
    "foo@bar.com",
    "fooo@barr.com"
]);
