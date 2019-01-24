<?php

use System\Application;
use System\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Composer
 */
$loader = require __DIR__ . '/vendor/autoload.php';

//$loader = require 'vendor/autoload.php';
//$loader->addPsr4('App\\', 'App');
//$loader->addPsr4('App\\Config\\', 'App/Configs');
//$loader->addPsr4('App\\Controller\\', 'App/Controllers');
//$loader->addPsr4('App\\Model\\', 'App/Models');
//$loader->addPsr4('App\\View\\', 'App/View');
$loader->addPsr4('System\\', 'System');

//froala editor
//require 'lib/FroalaEditor.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('System\Error::errorHandler');
set_exception_handler('System\Error::exceptionHandler');


$app = new \System\Application(dirname(__FILE__));
$request = Request::createFromGlobals();

//  more routes
// $app->addRoute('r/test', ['controller' => 'CustomRoute']);
$response = $app->run($request);


