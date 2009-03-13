<?php


require_once 'Zend/Tool/Project/Context/Interface.php';
require_once 'Zend/Reflection/File.php';

class Zend_Tool_Project_Context_Zf_ActionMethod implements Zend_Tool_Project_Context_Interface 
{
    /**
     * @var Zend_Tool_Project_Profile_Resource
     */
    protected $_resource = null;
    
    protected $_controllerResource = null;
    
    protected $_controllerPath = '';

    protected $_actionName = null;
    
    public function init()
    {
        $this->_actionName = $this->_resource->getAttribute('actionName');
        
        $this->_resource->setAppendable(false);
        $this->_controllerResource = $this->_resource->getParentResource();
        if (!$this->_controllerResource->getContext() instanceof Zend_Tool_Project_Context_Zf_ControllerFile) {
            require_once 'Zend/Tool/Project/Context/Exception.php';
            throw new Zend_Tool_Project_Context_Exception('ActionMethod must be a sub resource of a ControllerFile');
        }
        // make the ControllerFile node appendable so we can tack on the actionMethod.
        $this->_resource->getParentResource()->setAppendable(true);
        
        $this->_controllerPath = $this->_controllerResource->getContext()->getPath();
        
        if ($this->_controllerPath != '' && self::hasActionMethod($this->_controllerPath, $this->_actionName)) {
            require_once 'Zend/Tool/Project/Context/Exception.php';
            throw new Zend_Tool_Project_Context_Exception('An action named ' . $this->_actionName . 'Action already exists in this controller');
        }
        
    }
    
    public function getName()
    {
        return 'ActionMethod';
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
    
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }
    
    public function create()
    {
        if (self::createActionMethod($this->_controllerPath, $this->_actionName) === false) {
            require_once 'Zend/Tool/Project/Context/Exception.php';
            throw new Zend_Tool_Project_Context_Exception(
                'Could not create action within controller ' . $this->_controllerPath 
                . ' with action name ' . $this->_actionName
                );
        }
        return;
    }
    
    public function delete()
    {
        
    }
    
    
    public static function createActionMethod($controllerPath, $actionName, $body = '        // action body')
    {
        if (!file_exists($controllerPath)) {
            return false;
        }
        
        $controllerCodeGenFile = Zend_CodeGenerator_Php_File::fromReflectedFilePath($controllerPath);
        $controllerCodeGenFile->getClass()->setMethod(array(
            'name' => $actionName . 'Action',
            'body' => $body
            ));
        
        file_put_contents($controllerPath, $controllerCodeGenFile->generate());
        return true;
    }
    
    public static function hasActionMethod($controllerPath, $actionName)
    {
        if (!file_exists($controllerPath)) {
            return false;
        }
            
        $controllerCodeGenFile = Zend_CodeGenerator_Php_File::fromReflectedFilePath($controllerPath);
        return $controllerCodeGenFile->getClass()->hasMethod($actionName . 'Action');
    }
    
}