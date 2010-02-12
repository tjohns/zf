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
                          'parentType' => null)
    );
    
    public function __construct($options = array()) {
        $this->addOptions($options);
    }
    
    public static function factory($adapter, $config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        /*
         * Convert Zend_Config argument to plain string
         * adapter name and separate config object.
         */
        if ($adapter instanceof Zend_Config) {
            if (isset($adapter->params)) {
                $config = $adapter->params->toArray();
            }
            if (isset($adapter->adapter)) {
                $adapter = (string) $adapter->adapter;
            } else {
                $adapter = null;
            }
        }

        /*
         * Verify that adapter parameters are in an array.
         */
        if (!is_array($config)) {
            /**
             * @see Zend_Rbac_Exception
             */
            require_once 'Zend/Rbac/Exception.php';
            throw new Zend_Db_Exception('Adapter parameters must be in an array or a Zend_Config object');
        }

        /*
         * Verify that an adapter name has been specified.
         */
        if (!is_string($adapter) || empty($adapter)) {
            /**
             * @see Zend_Rbac_Exception
             */
            require_once 'Zend/Rbac/Exception.php';
            throw new Zend_Rbac_Exception('Adapter name must be specified in a string');
        }

        /*
         * Form full adapter class name
         */
        $adapterNamespace = 'Zend_Rbac_Adapter';
        if (isset($config['adapterNamespace'])) {
            if ($config['adapterNamespace'] != '') {
                $adapterNamespace = $config['adapterNamespace'];
            }
            unset($config['adapterNamespace']);
        }

        // Adapter should not be normalized - see http://framework.zend.com/issues/browse/ZF-5606
        $adapterName = $adapterNamespace . '_';
        $adapterName .= str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));

        /*
         * Load the adapter class.  This throws an exception
         * if the specified class cannot be loaded.
         */
        if (!class_exists($adapterName)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($adapterName);
        }

        /*
         * Create an instance of the adapter class.
         * Pass the config to the adapter class constructor.
         */
        $rbacAdapter = call_user_func($adapterName .'::setup',$config);

        /*
         * Verify that the object created is a descendent of the abstract adapter type.
         */
        if (! $rbacAdapter instanceof Zend_Rbac_Adapter_Abstract) {
            /**
             * @see Zend_Rbac_Exception
             */
            require_once 'Zend/Rbac/Exception.php';
            throw new Zend_Rbac_Exception("Adapter class '$adapterName' does not extend Zend_Rbac_Adapter_Abstract");
        }

        return $rbacAdapter;
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
    	if($object instanceof Zend_Rbac_Object) {
            if($this->_isObjectRegistered($type, $object)) {
                throw new Zend_Rbac_Exception(
                    "Cannot add $type with name {$object} twice"
                );
            }
            
            if($object->getType() != $type) {
                throw new Zend_Rbac_Exception('Given object is not of type '.$type);
            }
            
            $this->{$this->_objectTypes[$type]['container']}[(string)$object->getName()] = $object;
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
        
        throw new Zend_Rbac_Exception('Invalid object supplied');        
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
        if(!isset($this->{$this->_objectTypes[$type]['container']}[(string)$name])) {
    		if($throwException) {
        		throw new Zend_Rbac_Exception(
        		  'Could not retrieve an object that was never set', 'noSuchObject'
                );
    		} else {
    			return null;
    		}
    	}
    	
    	return $this->{$this->_objectTypes[$type]['container']}[(string)$name];
    }
    
    protected function _isObjectRegistered($type, $object)
    {
       return isset($this->{$this->_objectTypes[$type]['container']}[(string)$object]);
    }
    
    protected function _setParents($type, $childs, $parents) {
        $parentType = $this->_objectTypes[$type]['parentType'];

        if(!is_array($parents)) {
        	$parents = array($parents);
        }
        
        foreach ( $parents as $parent) {
        	//@todo Check for stringness, en typeness?
            if(!$this->_isObjectRegistered($parentType, $parent)) {
                throw new Zend_Rbac_Exception (
                    'Tried to assign an unexisting parent '
                );
            }
            
            if (! is_object ( $parent )) {
                $parent = $this->{$this->_objectTypes[$parentType]['container']}[( string ) $parent]; //@todo getParent
            } 
            /*elseif($parent->getType() != $parentType) {
               throw new Zend_Rbac_Exception (
                    'Invalid type of parent specified'
                );
            }*/
            
            foreach ( (array) $childs as $child) {
                if(!$this->_isObjectRegistered($type, $child)) {
                    throw new Zend_Rbac_Exception (
                        'Tried to assign an unexisting child to parent'
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
    
    protected function _assert($type, Zend_Rbac_Object $param1, $param2, $param3 = null) {
    	$methodName = 'assert'.strtoUpper($type);
    	if($param1->hasAssertions()) {
    		
            foreach($param1->getAssertions() as $id => $object) {
                if(!$object->$methodName($param1, $param2, $param3)) {
                	return false;
                }
            }
    	}

    	return true;
    }
    
    public function isAllowed($subjects, $resources)
    {
        if(is_string($subjects)) {
            $subjects = array($this->_getObject('subject', $subjects));
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
        	
        	$roles = $subject->getParents();
        	if(!$this->_assert('subject', $subject, $resources, $roles)) {
        		return false;
        	}
        	
            if(!$this->isAllowedRole($roles, $resources, $subject)) {
                return false;
            }
        }

        return true;
    }
    
    protected function _isAllowedRole($roles, $resources, $subject = null) {
        $result = array();
        foreach($resources as $resource) {
            $result[(string)$resource] = null;
        }
        $resultOrig = $result;
        
        foreach($roles as $roleId => &$role) {
        	if(is_string($role)) {
                $role = $this->_getObject('role', $role);
            }
            
            if(!$this->_assert('role', $role, $resources, $subject)) {
            	unset($roles[$roleId]);
            	continue;
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
            		$obj = $this->_getObject('resource', $resource);
                    $result[(string)$resource] = $this->_assert('resource', $obj, $role, $subject);
                }
            }

            unset($roles[$roleId]);
        }
        
        if(!empty($roles) && in_array(null, $result)) { // Some resources weren't satisfied. Try childs
        	/** Recursion here: **/
        	$subRes = $this->_isAllowedRole($roles, array_keys(array_intersect_assoc($result,$resultOrig)), $subject);
            foreach($subRes as $key => $value) {
            	$result[$key] = $value;
            }
        }
        
        return $result;
    }
    
    public function isAllowedRole($roles, $resources, $subject = null) {
        if(!is_array($roles)) {
            $roles = array($roles);
        }
        
        if(!is_array($resources)) {
            $resources = array($resources);
        }
    	
        return !in_array(false, $this->_isAllowedRole($roles, $resources, $subject));
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
    
    public function getSubjects($method = self::AS_STRING)
    {
        return $this->_getObjects('subject', $method);
    }
    
    public function getSubject($name) {
        return $this->_getObject('subject', $name);
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
    
    public function getRoles($method = self::AS_STRING)
    {
        return $this->_getObjects('role', $method);
    }
    
    public function getRole($name) {
        return $this->_getObject('role', $name);
    }
    
    
    public function addResources($resources) {
    	if(!is_array($resources)) {
    		$resources = (array)$resources;
    	}

        foreach($resources as $resource) {
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
    
    public function getResources($method = self::AS_STRING)
    {
        return $this->_getObjects('resource', $method);
    }
    
    public function getResource($name) {
    	return $this->_getObject('resource', $name);
    }
}
