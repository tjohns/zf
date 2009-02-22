<?php
/**
 * First the user must set up the PHP enviroment, which means, set the include
 * path and require the Zend_Application.
 */
set_include_path(dirname(__FILE__) . '/library' . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Application.php';

/**
 * Now there are multiple ways to set up the application. Firstly, the way via
 * options or Zend_Config:
 */
$application = new Zend_Application(APPLICATION_ENV, array(
    'bootstrap' => APPLICATION_PATH . '/Bootstrap.php',
    'resources' => array(
        'frontController' => array(
            'controllerDirectory' => APPLICATION_PATH . '/controllers',
        ),
    ),
));

/**
 * Alternatively, programatically:
 */
$application = new Zend_Application(APPLICATION_ENV);
$application->setBootstrap(APPLICATION_PATH . '/Bootstrap.php');
$bootstrap = $application->getBootstrap();
$bootstrap->registerPluginResource('frontController', array(
    'controllerDirectory' => APPLICATION_PATH . '/controllers',
));

/**
 * Finally, we can initiate either single plugins:
 */
$application->getBootstrap()->bootstrapFrontController();

/**
 * Or init all plugins at once, FIFO:
 */
$application->bootstrap();
