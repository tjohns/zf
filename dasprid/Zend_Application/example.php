<?php
/**
 * First the user must set up the PHP enviroment, which means, set the include
 * path and require the Zend_Application.
 */
set_include_path(dirname(__FILE__) . '/library');

require_once 'Zend/Application.php';

/**
 * Now there are multiple ways to set up the application. Firstly, the way via
 * options or Zend_Config:
 */
$application = new Zend_Application(array(
    'loader' => array(
        'class'   => 'myPrivateLoader',
        'enabled' => true
    )
));

/**
 * Alternatively, half programatically:
 */
$application = new Zend_Application();
$application->registerPlugin('loader', array('class' => 'myPrivateLoader',
                                             'enabled' => true));

/**
 * Or even complete programatically:
 */
$application = new Zend_Application();
$application->registerPlugin('loader'); // Should this be fluent or return the
                                        // instantiated plugin?

$loaderPlugin = $application->getPlugin('loader');
$loaderPlugin->setClass('myPrivateLoader')
             ->setEnabled(true);
             
/**
 * Finally, we can initiate either single plugins:
 */
$application->initLoader();

/**
 * Or init all plugins at once, FIFO:
 */
$application->initAll();

/**
 * What's yet missing, but will be added, is a setPluginPath() method, which
 * will allow to set an include path for user generated plugins.
 */