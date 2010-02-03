<?php
require_once 'Zend/Rbac/Subject.php';

class Zend_Rbac
{
    const AS_OBJECT = 'AS_OBJECT';
    const AS_STRING = 'AS_STRING';
    
    protected $_subjects = array();
    
    protected $_roles = array();
    
    protected $_resources = array();
    
    protected $_objectTypes = array(
       'subject' => array('isRegistered' => 'isSubjectRegistered',
                          'container' => '_subjects',
                          'class' => 'Zend_Rbac_Subject'),
       'role'    => array('isRegistered' => 'isRoleRegistered',
                          'container' => '_roles',
                          'class' => 'Zend_Rbac_Role'),
       'resource'=> array('isRegistered' => 'isResourceRegistered',
                          'container' => '_resources',
                          'class' => 'Zend_Rbac_Resource')
    );
    
    protected $_strictMode = false;
    
    public function __construct($options = null) {
        $this->addOptions($options);
    }
    
    public function addOptions($options) {
        $options = array_change_key_case($options);
        foreach($options as $key => $value) {
            switch($key) {
                case 'subject':
                case 'subjects':
                    $this->addSubjects($value);
                    break;
                case 'role':
                case 'roles':
                    $this->addRoles($value);
                    break;
                case 'resource':
                case 'resources':
                    $this->addResources($value);
                    break;
            }
        }
    }
    
    protected function _addObject($type, $object) {
        if($type instanceof Zend_Rbac_ObjectInterface) {
            if($this->_isObjectRegistered($type, $object)) {
                throw new Zend_Rbac_Exception(
                    "Cannot add $type with name {$object} twice"
                );
            }
            
            if($object->getType() != $type) {
                throw new Zend_Rbac_Exception('Given object is not of type '.$type);
            }
            
            $this->{$$this->_objectTypes[$type]['container']}[(string)$object] = $object;
            return $this;
        } elseif(is_string($object) ||
                (is_object($object) && is_callable(array($object, '__toString'))))
        {
            if($this->_isObjectRegistered($type, $object)) {
                throw new Zend_Rbac_Exception(
                    "Cannot add {$type} with name {$object} twice"
                );
            }

            $class = $this->_objectTypes[$type]['class'];
            $name = (string)$object;
            $this->{$this->_objectTypes[$type]['container']}[$name] = new $class($name);
            return $this;
        }
        
        throw new Zend_Rbac_Exception('Invalid subject supplied');        
    }
    
    protected function _getObjects($type, $method)
    {
        if($method == self::AS_OBJECT) {
           return $this->{$this->_objectTypes[$type]['container']};
        }

        if(true || $method == self::AS_STRING) { // default
            $out = array();
            foreach($this->{$this->_objectTypes[$type]['container']} as $object) {
                $out[] = $object->__toString();
           }
           
           return $out;
        }        
    }
    
    protected function _isObjectRegistered($type, $object)
    {
       return isset($this->{$this->_objectTypes[$type]['container']}[(string)$object]);
    }
    
    public function assignRole($roles, $subjects, $mode = null) {
        if($mode === null) {
            foreach((array)$roles as $role) {
                if(!$this->isRoleRegistered($role)) {
                    throw new Zend_Rbac_Exception(
                       "Tried to assign subject to unexisting role ".(string)$role
                    );
                }
                
                foreach((array)$subjects as $subject){
                    if(!$this->isRoleRegistered($role)) {
                        throw new Zend_Rbac_Exception(
                            "Tried to assign unexisting subject ".((string)$role)."to role"
                        );
                    }
                    
                    //@todo continue here
                }
            }
        }
    }
    
    public function isSubjectRegistered($subject) {
        return $this->_isObjectRegistered('subject', $subject);
    }
    
    public function addSubjects($subjects) {
        foreach((array) $subjects as $subject) {
            $this->addSubject($subject);
        }
        
        return $this;
    }
    
    public function addSubject($subject) {
        $this->_addObject('subject', $subject);
        
        return $this;
    }
    
    public function getSubjects($method = 'AS_STRING')
    {
        return $this->_getObjects('subject', $method);
    }
    
    public function isRoleRegistered($role) {
        return $this->_isObjectRegistered('role', $role);
    }
    
    
    public function addRoles($roles) {
        foreach((array) $roles as $role) {
            $this->addRole($role);
        }
        
        return $this;
    }
    
    public function addRole($role) {
        $this->_addObject('role', $role);
        
        return $this;
    }
    
    public function getRoles($method = 'AS_STRING')
    {
        return $this->_getObjects('role', $method);
    }
    
    public function addResources($resources) {
        foreach((array) $resources as $resource) {
            $this->addResource($resource);
        }
        
        return $this;
    }
    
    public function isResourceRegistered($resource) {
        return $this->_isObjectRegistered('resource', $resource);
    }
    
    
    public function addResource($resource) {
        $this->_addObject('resource', $resource);
        
        return $this;
    }
    
    public function getResources($method = 'AS_STRING')
    {
        return $this->_getObjects('resource', $method);
    }
        
}
