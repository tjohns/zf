<?php

require_once 'Zend/Console/Getopt.php';
require_once 'Zend/Loader/PluginLoader.php';

class ZfTool
{
    /**
     * @var ZfTool
     */
    static protected $_instance = null;
    
    protected $_zfToolPath = null;
    
    /**
     * @var Zend_Console_CommandParser
     */
    protected $_commandParser = null;
    protected $_globalOptions = array();
    protected $_actionOptions = array();
    protected $_resourceOptions = array();
    
    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_actionPluginLoader = null;
    
    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_resourcePluginLoader = null;
    
    protected $_actions = array();
    protected $_resources = array();
    
    static public function getInstance()
    {
        if (!self::$_instance instanceof Zf_Cli) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    protected function __construct()
    {
        $this->_commandParserInit();
    }
        
    public function setZfToolPath($zfLibraryPath)
    {
        $this->_zfToolPath = rtrim($zfLibraryPath, '\/') . DIRECTORY_SEPARATOR;
        return $this;
    }
    
    public function getZfToolPath()
    {
        if ($this->_zfToolPath == null) {
            $this->setZfToolPath(dirname(dirname(dirname(__FILE__))));
        }
        
        return $this->_zfToolPath;
    }
    
    public function run()
    {
        try {
            $this->_commandParser->parse();
        } catch (Exception $exception) {
            echo $exception->getMessage();
            echo $this->_getUsage();
            exit(0);
        }
        
        $this->_executeRequest();

    }
    
    
    protected function _commandParserInit()
    {
        $this->_commandParser = new Zend_Console_CommandParser();
        $this->_commandParser->appendParser(
            'globalOptions',
            new Zend_Console_CommandParser_GetoptParser(array(
                'help|h' => 'Help option.',
                'verbose|v' => 'Verbosity',
                'projectDirectory|p=s' => 'Project directory.'
                )),
            array($this, '_commandParserProcessGlobalOptionHandler')
            );
    }
    
    public function _commandParserProcessGlobalOptionHandler()
    {
        $args = func_get_args();
        $results = $args[1];

        $this->_loadActionsAndResources();
        $this->_commandParserActionParser();
    }
    
    protected function _commandParserActionParser()
    {

        $this->_commandParser->appendParser(
            'actionName',
            new Zend_Console_CommandParser_StringParser(array('actionName'))
            );

        foreach ($this->_actions as $action) {
            $actionOptions = $action->getGetoptOptions();
        }
        
        $this->_commandParser->appendParser(
            'actionOptions',
            new Zend_Console_CommandParser_GetoptParser(array()),
            array($this, '_commandParserActionHandler')
            );
    }
    
    public function _commandParserActionHandler()
    {
        
    }
    
    protected function _commandParserResourceParser()
    {
        /*
        foreach ($this->_resources as $resource) {
            array_merge($resourceOptions, $resource->getGetoptConfig());
        }
        */       
    }
    
    protected function _commandParserResourceHandler()
    {

    }
    
    
    protected function _loadActionsAndResources()
    {
        $zfToolPath = $this->getZfToolPath();

        $zfToolBuildPath = 'library' . DIRECTORY_SEPARATOR
            . 'ZfTool' . DIRECTORY_SEPARATOR 
            . 'Build' . DIRECTORY_SEPARATOR;
        
        $this->_actionPluginLoader = new Zend_Loader_PluginLoader(array(
           'ZfTool_Build_Action_' => $zfToolPath . $zfToolBuildPath . 'Action'
            ));
            
        foreach ($this->_actionPluginLoader->loadAll() as $classLoaded) {
            $classLoadedObj = new $classLoaded();
            if (!$classLoadedObj instanceof Zend_Console_Getopt_Provider_Interface) {
                require_once 'ZfTool/Exception.php';
                throw new ZfTool_Exception('The action ' . $classLoaded . ' must implement Zend_Console_Getopt_Provider_Interface.');
            }
            $this->_actions[$classLoaded] = $classLoadedObj;
        }

        $this->_resourcePluginLoader = new Zend_Loader_PluginLoader(array(
            'ZfTool_Build_Resource_' => $zfToolPath . $zfToolBuildPath . 'Resource/'
            ));
            
        foreach ($this->_resourcePluginLoader->loadAll() as $classLoaded) {
            $classLoadedObj = new $classLoaded();
            if (!$classLoadedObj instanceof Zend_Console_Getopt_Provider_Interface) {
                require_once 'ZfTool/Exception.php';
                throw new ZfTool_Exception('The resource ' . $classLoaded . ' must implement Zend_Console_Getopt_Provider_Interface.');
            }
            $this->_resources[$classLoaded] = $classLoadedObj;
        }

    }
    
    protected function _getUsage()
    {
        echo 'usage';
    }
    
    protected function _setupEnvironment()
    {
        
    }
    
    protected function _executeRequest()
    {
        Zend_Debug::dump($this->_globalOptions);
        if (!isset($this->_globalOptions['projectDirectory'])) {
            die('no project directory set');
        }
        
        
        
    }

}
