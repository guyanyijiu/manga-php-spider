<?php

$GLOBALS['config']['db'] = array(
    'host'  => '127.0.0.1',
    'port'  => 3306,
    'user'  => 'root',
    'pass'  => 'MJU&6yhn',
    'name'  => 'manga',
);

$GLOBALS['config']['redis'] = array(
    'host'      => '127.0.0.1',
    'port'      => 6379,
    'pass'      => '',
    'prefix'    => 'phpspider',
    'timeout'   => 30,
);

include "inc_mimetype.php";
