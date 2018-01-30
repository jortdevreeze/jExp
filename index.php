<?php
/**
* +---------------------------------------------------------------------------+
* | Copyright (c) 2015, Jort de Vreeze                                        |
* | All rights reserved.                                                      |
* |                                                                           |
* | Redistribution and use in source and binary forms, with or without        |
* | modification, are not permitted.                                          |
* +---------------------------------------------------------------------------+
* | jExp 1.0                                                                  |
* +---------------------------------------------------------------------------+
* | index.php                                                                 |
* +---------------------------------------------------------------------------+
* | Author: Jort de Vreeze <j.devreeze@iwm-tuebingen.de>                      |
* +---------------------------------------------------------------------------+
*/

version_compare(
    PHP_VERSION, '5.0.0', '>') or die('This framework requires at least PHP 5.0.0, you have ' . PHP_VERSION . '.' . PHP_EOL
);

/*
 * Needed for cross-domain AJAX requests
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

/*
 * The main configuration for the application
 */
$config1 = parse_ini_file("settings.ini", true);
$config2 = array(
    'routes' => array(
        'json'  => array(),
        'user'  => array(),
        'login' => array()
    ),
    'access' => array(
        'page' => array('*' => 'false'),
        '*' => 'true'
    )
);

$config = array_merge($config1, $config2);

/*
 * Setup all paths required for the application
 */
if (is_dir('library')) {
    $basepath = realpath('library') . '/';	
} else {
    exit("Your library folder does not appear to be set correctly.");
}

if (is_dir('application')) {
    $apppath = realpath('application') . '/';	
} else {
    exit("Your application folder does not appear to be set correctly.");
}

$apppath = str_replace("\\", "/", $apppath);

/*
 * Save path names as global variables
 */
define('B_PATH', str_replace("\\", "/", $basepath));
define('C_PATH', $apppath . 'controllers/');
define('M_PATH', $apppath . 'models/');
define('V_PATH', $apppath . 'views/');

/*
 * Setup the class autoloader
 */
function classLoader($class)
{
    $parts = explode('_', $class);
    $namespace = $parts[0];
    $classname = $parts[1];
    
    switch($namespace)
    {
        case 'Base':
            $filename = B_PATH . $classname . '.php';
            break;
        case 'Controller':
            $filename = C_PATH . $classname . '.php';
            break;
        case 'Model':
            $filename = M_PATH . $classname . '.php';
            break;
    }

    if (!file_exists($filename)) {
        return false;
    }
    require_once $filename;
}

spl_autoload_register('classLoader');

/*
 * Dispatch the request to the front controller
 */
$dispatcher = new Base_Dispatcher();
$dispatcher->setConfiguration($config);
$dispatcher->dispatch();