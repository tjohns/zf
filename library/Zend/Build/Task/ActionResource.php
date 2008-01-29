<?php

require_once 'Zend/Build/Task/Abstract.php';

class Zend_Build_Task_ActionResource extends Zend_Build_Task_Abstract
{
    /**
     * @var Zend_Build_Action_Abstract
     */
    protected $_action = null;
    
    /**
     * @var Zend_Build_Resource_Abstract
     */
    protected $_resource = null;
    
    public function __construct(Zend_Build_Action_Abstract $action, Zend_Build_Resource_Abstract $resource = null)
    {
        if (!$resource->implementsActionName($action->getName())) {
            throw new Zend_Build_Task_Exception('Resource ' . $resource->getName() . ' does not implement action ' . $action->getName());
        }

        $this->_action = $action;
        $this->_resource = $resource;
    }
    
    public function satisfyDependencies()
    {
        $this->_action->satisfyDependencies();
        $this->_resource->satisfyDependencies();
        
        $resourceActionDependencyMethod = $this->_action->getName() . 'Dependencies';
        if (method_exists($this->_resource, $resourceActionDependencyMethod)) {
            $this->_resource->$resourceActionDependencyMethod();
        }

        return $this;
    }
    
    public function setup()
    {
        $this->_action->setup();
        $this->_resource->setup();
        return $this;
    }
    
    public function rollback()
    {
        $this->_action->rollback();
        $this->_resource->rollback();
        return $this;
    }
    
    public function cleanup()
    {
        $this->_action->cleanup();
        $this->_resource->cleanup();
        return $this;
    }
    
    public function execute()
    {
        $methodName = $this->_action->getName() . 'Action';
        $this->_resource->$methodName();
    }
    
}