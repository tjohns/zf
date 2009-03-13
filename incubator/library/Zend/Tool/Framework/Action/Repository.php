<?php

class Zend_Tool_Framework_Action_Repository implements IteratorAggregate
{
    protected $_actions = array();
    
    public function addAction($action)
    {
        if (is_string($action)) {
            $actionName = $action;
            $action = new Zend_Tool_Framework_Action_Base();
            $action->setName($actionName);
        }

        if (!$action instanceof Zend_Tool_Framework_Action_Interface) {
            require_once 'Zend/Tool/Framework/Action/Exception.php';
            throw new Zend_Tool_Framework_Action_Exception(
                'Action must be an instance of Zend_Tool_Framework_Action_Interface or an action name.'
            );
        }

        if (!array_key_exists($action->getName(), $this->_actions)) {
            $this->_actions[$action->getName()] = $action;
        }

        return $this;
    }

    public function process()
    {
        
    }
    
    public function getActions()
    {
        return $this->_actions;
    }

    public function getAction($actionName, $createIfNotExist = false)
    {
        if (!in_array($actionName, $this->_actions)) {
            $this->addAction($actionName);
        }

        return $this->_actions[$actionName];
    }
    
    public function getIterator()
    {
        return $this->_actions;
    }
}
