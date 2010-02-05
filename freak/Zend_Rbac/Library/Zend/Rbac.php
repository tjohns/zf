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
                          'class' => 'Zend_Rbac_Subject',
                          'parentType' => 'role'),
       'role'    => array('isRegistered' => 'isRoleRegistered',
                          'container' => '_roles',
                          'class' => 'Zend_Rbac_Role',
                          'parentType' => 'resource'),
       'resource'=> array('isRegistered' => 'isResourceRegistered',
                          'container' => '_resources',
                          'class' => 'Zend_Rbac_Resource',
                          'parent' => null)
    );
    
    protected $_strictMode = false;
    
    public function __construct($options = array()) {
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
    
    protected function _getObject($type, $name, $throwException = true) {
    	if(!isset($this->{$this->_objectTypes[$type]['container']}[$name])) {
    		if($throwException) {
        		throw new Zend_Rbac_Exception(
        		  'Could not retrieve an object that was never set', 'noSuchObject'
    	       );
    		} else {
    			return null;
    		}
    	}
    	
    	return $this->{$this->_objectTypes[$type]['container']}[$name];
    }
    
    protected function _isObjectRegistered($type, $object)
    {
       return isset($this->{$this->_objectTypes[$type]['container']}[(string)$object]);
    }
    
    protected function _setParents($type, $childs, $parents) {
        $parentType = $this->_objectTypes[$type]['parentType'];

        foreach ( (array) $parents as $parent) {
            if(!$this->_isObjectRegistered($parentType, $parent)) {
                throw new Zend_Rbac_Exception (
                    'Tried to assign an unexisting parent'
                );
            }
            
            if (! is_object ( $parent )) {
                $parent = $this->{$this->_objectTypes[$parentType]['container']}[( string ) $parent];
            } elseif($parent->getType() != $parentType) {
                throw new Zend_Rbac_Exception (
                    'Invalid type of parent specified'
                );
            }
            
            foreach ( (array) $childs as $child) {
                if(!$this->_isObjectRegistered($type, $child)) {
                    throw new Zend_Rbac_Exception (
                        'Tried to assign an unexisting child'
                    );
                }            	
            	
                if (! is_object ( $child )) {
	                $child = $this->{$this->_objectTypes[$type]['container']}[( string ) $child];
	            } elseif($child->getType() != $type) {
	                throw new Zend_Rbac_Exception (
	                    'Invalid type of child specified'
	                );
	            }
            
                $child->addParent ( $parent);
            }
        }
    }
    
    public function isAllowed($subjects, $resources)
    {
        if(is_string($subjects)) {
            $subjects = array($this->_getObject('subject', $subjects));
            if($subjects === array(null)) {
            	if($this->strictMode()) {
            		
            	}
            }
        } elseif(!is_array($subjects)) {
            $subjects = array($subjects);
        }

        if(!is_array($resources)) {
            $resources = array($resources);
        }
        
        foreach($subjects as $subject) {
        	if($subject !== $this->_subjects[$subject->getName()]) {
        		throw new Zend_Rbac_Exception('No subject "'.$subject->getName().'"');
        	}
        	
            if(!$this->isAllowedRole($subject->getParents(), $resources)) {
                return false;
            }
        }

        return true;
    }
    
    protected function _isAllowedRole($roles, $resources) {
        $result = array();
        foreach($resources as $resource) {
            $result[(string)$resource] = false;
        }
        $resultOrig = $result;
        
        foreach($roles as $roleId => &$role) {
        	if(is_string($role)) {
                $role = $this->_getObject('role', $role);
            }
            
            $parents = $role->getParents();
            
            foreach($parents as $id => $parent) {
            	if($parent->getType() == 'role') {
            		$roles[] = $parent;
            		unset($parents[$id]);
            	}
            }
            
            foreach($resources as $resource) {
                if(in_array((string)$resource, $parents)) {
                    $result[(string)$resource] = true;
                }
            }
            unset($roles[$roleId]);
        }
        
        if(!empty($roles) && in_array(false, $result)) { // Some resources weren't satisfied. Try childs
        	$subRes = $this->_isAllowedRole($roles, array_keys(array_intersect_assoc($result,$resultOrig)));
            foreach($subRes as $key => $value) {
            	$result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    public function isAllowedRole($roles, $resources) {
        if(!is_array($roles)) {
            $roles = array($roles);
        }
        
        if(!is_array($resources)) {
            $resources = array($resources);
        }
    	
        return !in_array(false, $this->_isAllowedRole($roles, $resources));
    }
    
    public function assignRoles($roles, $subjects) {
        $this->_setParents('subject', $subjects, $roles);
        return $this;
    }
    
    public function addChild($parent, $child) {
    	$parent = $this->_getObject('role', $parent);
    	$child = $this->_getObject('role', $child);
    	
    	$parent->addParent($child);
    	return $this;
    }
    
    public function subscribe($resources, $roles) {
    	$this->_setParents('role', $roles, $resources);
    	return $this;
    }
    
    public function isSubjectRegistered($subject) {
        return $this->_isObjectRegistered('subject', $subject);
    }
    
    /** Getters & Setters below **/
    
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
    
    public function setStrictMode($on = true) {
    	if(!is_boolean($on)) {
    		throw new Zend_Rbac_Exception('Boolean excepted, but none given');
    	}
    	
    	$this->_strictMode = $on;
    	return $this;
    }
    
    public function strictMode() {
    	return $this->_strictMode;
    }
        
}
