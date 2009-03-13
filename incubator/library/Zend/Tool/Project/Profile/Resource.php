<?php

require_once 'Zend/Tool/Project/Profile/Resource/Container.php';
require_once 'Zend/Tool/Project/Context/Repository.php';

class Zend_Tool_Project_Profile_Resource extends Zend_Tool_Project_Profile_Resource_Container
{
    
    protected $_profile = null;
    protected $_parentResource = null;

    /**#@+
     * @var bool
     */
    protected $_deleted = false;
    protected $_enabled = true;

    /**
     * @var Zend_Tool_Project_Context|string
     */
    protected $_context = null;

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * @var bool
     */
    protected $_isContextInitialized = false;

    public function __construct($context)
    {
        $this->setContext($context);
    }

    public function setContext($context)
    {
        $this->_context = $context;
        return $this;
    }

    public function getContext()
    {
        return $this->_context;
    }

    public function getName()
    {
        if (is_string($this->_context)) {
            return $this->_context;
        } elseif ($this->_context instanceof Zend_Tool_Project_Context_Interface) {
            return $this->_context->getName();
        } else {
            throw new Zend_Tool_Project_Exception('Invalid context in resource');
        }
    }

    public function setProfile(Zend_Tool_Project_Profile $profile)
    {
        $this->_profile = $profile;
        return $this;
    }

    public function getProfile()
    {
        return $this->_profile;
    }

    public function getPersistentAttributes()
    {
        if (method_exists($this->_context, 'getPersistentAttributes')) {
            return $this->_context->getPersistentAttributes();
        }

        return array();
    }

    public function setEnabled($enabled = true)
    {
        // convert fuzzy types to bool
        $this->_enabled = (!in_array($enabled, array('false', 'disabled', 0, -1, false), true)) ? true : false;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }

    public function setDeleted($deleted = true)
    {
        $this->_deleted = (bool) $deleted;
        return $this;
    }

    public function isDeleted()
    {
        return $this->_deleted;
    }
    
    public function initializeContext()
    {
        if ($this->_isContextInitialized) {
            return;
        }
        if (is_string($this->_context)) {
            $this->_context = Zend_Tool_Project_Context_Repository::getInstance()->getContext($this->_context);
        }
        
        if (method_exists($this->_context, 'setResource')) {
            $this->_context->setResource($this);
        }
        
        if (method_exists($this->_context, 'init')) {
            $this->_context->init();
        }
        
        $this->_isContextInitialized = true;
    }

    public function __toString()
    {
        return $this->_context->getName();
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->_context, $method)) {
            if (!$this->isEnabled()) {
                $this->setEnabled(true);
            }
            return call_user_func_array(array($this->_context, $method), $arguments);
        } else {
            throw new Zend_Tool_Project_Profile_Exception('cannot call ' . $method);
        }
    }

}