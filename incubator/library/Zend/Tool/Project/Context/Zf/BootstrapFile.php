<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

class Zend_Tool_Project_Context_Zf_BootstrapFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    protected $_filesystemName = 'bootstrap.php';
    
    public function getName()
    {
        return 'BootstrapFile';
    }
    
    public function getContents()
    {
        $body = <<<EOS
<?php

defined('APPLICATION_PATH') 
    or define('APPLICATION_PATH', realpath(dirname(__FILE__)));

defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');
    
// ** Check to see if the environment is already setup **
if (isset(\$bootstrap) && \$bootstrap) {
    // Enable all errors so we'll know when something goes wrong.
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);

    // Add our {{library}} directory to the include path so that PHP can find the Zend Framework classes.
    // you may wish to add other paths here, or keep system paths: set_include_path('../library' . PATH_SEPARATOR . get_include_path()
    set_include_path('../library' . PATH_SEPARATOR . get_include_path());

    // Set up autoload.
    // This is a nifty trick that allows ZF to load classes automatically so that you don't have to litter your
    // code with 'include' or 'require' statements.
    require_once "Zend/Loader.php";
    Zend_Loader::registerAutoload();
} 
 
// ** Get the front controller **
// The Zend_Front_Controller class implements the Singleton pattern, which is a design pattern used to ensure
// there is only one instance of Zend_Front_Controller created on each request.
\$frontController = Zend_Controller_Front::getInstance();
 
// Point the front controller to your action controller directory. 
\$frontController->setControllerDirectory(APPLICATION_PATH . '/controllers'); 

// Set the current environment
// Set a variable in the front controller indicating the current environment --
// commonly one of development, staging, testing, production, but wholly
// dependent on your organization and site's needs.
\$frontController->setParam('env', 'development');

\$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENVIRONMENT);

EOS;
        
        
        $codeGenerator = new Zend_CodeGenerator_Php_File(array(
            'body' => $body 
             ));
        return $codeGenerator->generate();
    }
    
}