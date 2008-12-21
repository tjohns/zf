<?php

require_once 'Zend/Loader.php';

class Zend_Tool_Project_Context_Registry implements Countable
{
    
    protected static $_instance = null;
    protected static $_loadedSystem = false;
    protected static $_loadedZf = false;
    
    protected $_shortContextNames = array();
    protected $_contexts          = array();
    
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }

    public static function loadSystem()
    {
        if (self::$_loadedSystem) {
            return;
        }
        
        $contextRegistry = self::getInstance();
        $contextRegistry
            ->addContextClass('Zend_Tool_Project_Context_System_ProjectDirectory',   false)
            ->addContextClass('Zend_Tool_Project_Context_System_ProjectProfileFile', false)
            ->addContextClass('Zend_Tool_Project_Context_System_ProvidersDirectory', false);

        self::$_loadedSystem = true;
    }

    
    
    public static function loadZf()
    {
        if (self::$_loadedZf) {
            return;
        }
        
        $contextRegistry = self::getInstance();
        $prefix = 'Zend_Tool_Project_Context_Zf_';
        foreach (new DirectoryIterator(dirname(__FILE__) . '/Zf/') as $directoryItem) {
            if ($directoryItem->isDot() || (substr($directoryItem->getFilename(), -4) !== '.php')) {
                continue;
            }
            $class = $prefix . substr($directoryItem->getFilename(), 0, -4);
            $contextRegistry->addContextClass($class);
        }
        
        self::$_loadedZf = true;
    }
    
    public static function resetInstance()
    {
        self::$_instance = null;
        self::$_loadedSystem = false;
        self::$_loadedZf = false;
    }
    
    protected function __construct()
    {
    }
    
    public function addContextClass($contextClass, $overwriteable = true)
    {
        Zend_Loader::loadClass($contextClass);
        $context = new $contextClass();
        return $this->addContext($context, $overwriteable);
    }
    
    public function addContext(Zend_Tool_Project_Context_Interface $context, $overwriteable = true)
    {
        
        
        $index = (count($this->_contexts)) ? max(array_keys($this->_contexts)) + 1 : 1;
        
        $normalName = $this->_normalizeName($context->getName());
        
        if (isset($this->_shortContextNames[$normalName]) && ($this->_contexts[$this->_shortContextNames[$normalName]]['overwriteable'] === false) ) {
                require_once 'Zend/Tool/Project/Context/Exception.php';
                throw new Zend_Tool_Project_Context_Exception('Context ' . $context->getName() . ' is not overwriteable.');
        }
        
        $this->_shortContextNames[$normalName] = $index;
        $this->_contexts[$index]          = array(
            'overwriteable' => (bool) $overwriteable,
            'normalName'    => $normalName,
            'context'       => $context
            );
        
        //$this->_contexts[strtolower($context->getName())] = array('options' => null, 'context' => $context);
        return $this;
    }
    
    public function getContext($name)
    {        
        if (!$this->hasContext($name)) {
            require_once 'Zend/Tool/Project/Context/Exception.php';
            throw new Zend_Tool_Project_Context_Exception('Context by name ' . $name . ' does not exist in the registry.');
        }
        
        $name = $this->_normalizeName($name);
        return clone $this->_contexts[$this->_shortContextNames[$name]]['context'];
    }
    
    public function hasContext($name)
    {
        $name = $this->_normalizeName($name);
        return (isset($this->_shortContextNames[$name]) ? true : false);
    }
    
    public function count()
    {
        return count($this->_contexts);
    }
    
    protected function _normalizeName($name)
    {
        return strtolower($name);
    }
    
}