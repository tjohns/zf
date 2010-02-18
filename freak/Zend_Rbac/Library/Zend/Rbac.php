<?php
require_once 'Zend/Rbac/Subject.php';

class Zend_Rbac
{
    const AS_OBJECT = 'AS_OBJECT';
    const AS_STRING = 'AS_STRING';
    
    protected $_subjects = array();
    
    public $_roles = array();
    
    public $_resources = array();

    // Should be immutable/final:
    private $_objectTypes = array(
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
    
    protected function _getObjectTypes() {
    	return $this->_objectTypes;
    }
    
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
    
    public function isAllowedRole($role, $resources, $subject = null) {
        if(is_array($role)) {
        	throw new Zend_Rbac_Exception('Role cannot be an array. (not implemented yet)');
        }
        
        if(!is_array($resources)) {
        	$resources = array($resources);
        }
        
        return !in_array(null, $this->_isAllowedRole($role, $resources, $subject));
    }
    
    public function isAllowed($subjects, $resources) {
    	foreach((array) $subjects as $subject) {
            $subject = $this->_getObject('subject', $subject);
            foreach($subject['parents'] as $role => $foo) {
            	if($this->isAllowedRole($role, $resources, $subject)) {
            		return true;
            	}
            }
    	}

    	return false;
    }
    
    protected function _isAllowedRole($role, $resources, $subject = null) {
    	$result = $resultOrig = array_fill_keys($resources, null);
    	$role = $this->_getObject('role',$role);
    	
        $resourcesRole = array();
        $subRoles = array();
        foreach($role['parents'] as $id => $parent) {
        	if($parent['type']=='role') {
        		$subRoles[$id] = $parent;
        	} else {
        		$resourcesRole[$id] = $parent;
        	}
        }
        
        $tmp = array_intersect($resources, array_keys($resourcesRole));
        foreach($tmp as $moreTmp) {
        	$result[$moreTmp] = true;
        }
        
        if(in_array(null, $result)) {
        	foreach($subRoles as $id => $role) {
        	    $subRes = $this->_isAllowedRole(
            	       $role['name'],
            	       array_keys(array_intersect_assoc($result,$resultOrig)),
            	       $subject
                );        	    
  
                foreach($subRes as $key => $value) {
                    $result[$key] = $value;
                }
        	}
        }

        return $result;
    }

    protected function _assert($type, $param1, $param2, $param3 = null) {
    	return true; //@todo implement
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
    
    
    protected function _addObject($type, $object)
    {
    	if(is_string($object)) {
    		$objType = 'Zend_Rbac_Object_'.ucfirst($type);
    		$name = $object;
    		$object = new $objType($object);
    	} elseif(($expectedType = 'Zend_Rbac_'.ucfirst($type)) && 
    	         !$object instanceof $expectedType)
        {
    	   throw new Zend_Rbac_Exception(
    	       'Given object does not implement '.$expectedType
    	   );         	
    	} else {
    		$name = $object->__toString();
    	}
    	
    	if($this->_isObjectRegistered($type, $name)) {
    		throw new Zend_Rbac_Exception(
                'Cannot set same '.$type.' twice'
    		);
    	}
    	
    	$this->{$this->_objectTypes[$type]['container']}[$name]
    	   = array('object'        => $object,
    	           'name'          => $name,
    	           'type'          => $type,
    	           'parents'       => array(),
    	           'assertions'    => new Zend_Rbac_AssertionContainer()
       );
    
       return $this;
    }
    
    protected function _isObjectRegistered($type, $name)
    {
    	$container = $this->{$this->_objectTypes[$type]['container']};
        if(!isset($container[(string) $name])) {
        	return false;
        }

        if(is_object($name) && 
           ($container[(string) $name] !== $name ||
            $this->isObjectOfRightType($type, $name)))
        {
        	return false;
        }

        return true;
    }

    protected function _getObjects($type,$method)
    {
    	$objects = $this->{$this->_objectTypes[$type]['container']};
    	$out = array();

    	if($method == self::AS_OBJECT) {
    		foreach($objects as $name => $object) {
    			$out[$name] = $object['object'];
    		}
    	} elseif($method == self::AS_STRING) {
    	    foreach($objects as $name => $object) {
                $out[] = $object['name'];
            }    		
    	} else {
            throw new Zend_Rbac_Exception(
                'Unknown method requested'
            );
    	}
    	
    	return $out;
    }
    
    protected function &_getObject($type, $object)
    {
    	if(is_object($object)) {
        	if($this->getObjectType($object) != $type) {
        		throw new Zend_Rbac_Exception(
    	   	       'Given object is no instance of the right type'
               );
        	}        	
    	}
    	
        if(!$this->_isObjectRegistered($type, $object)) {
            throw new Zend_Rbac_Exception('Object '.((string)$object.' has not been registered: '.print_r($object,1)));
        }
    	
        return $this->{$this->_objectTypes[$type]['container']}[(string) $object];
    }
    
    public function getObjectTypes($object) {
    	if(!is_object($object)) {
    		throw new Zend_Rbac_Exception(
    		    'Given "object" is not an object'
    		);
    	}
    	
    	$out = array();
    	if($object instanceof Zend_Rbac_Subject) {
    		$out[] = 'subject';
    	} elseif($object instanceof Zend_Rbac_Role) {
    		$out[] = 'role';
    	} elseif($object instanceof Zend_Rbac_Resource) {
    		$out[] = 'resource';
    	}
    	
    	return $out;
    }
    
    public function getObjectType($object) {
    	return (string) $this->getObjectType;
    }
    
    public function isObjectOfRightType($type, $object)
    {
        return in_array($type, $this->getObjectTypes($object));
    }

    public function assignRoles($roles, $subjects)
    {
    	foreach( (array) $subjects as $subject) {
    		$subject = &$this->_getObject('subject', $subject);
    		
    		foreach( (array) $roles as $role) {
    			$role = &$this->_getObject('role', $role);
    			$subject['parents'][$role['name']] = &$role;
    		}
    	}
    	
    	return $this;
    }
    
    public function subscribe($resources, $roles)
    {
        foreach( (array) $roles as $role) {
            $role = &$this->_getObject('role', $role);
            
            foreach( (array) $resources as $resource) {
                $resource = &$this->_getObject('resource', $resource);
                var_dump($role, $resource);
                $role['parents'][$resource['name']] = &$resource;
            }
        }
        
        return $this;       
    }
    
    public function addChild($parents, $childs)
    {
        foreach( (array) $childs as $child) {
            $role = &$this->_getObject('role', $child);
            
            foreach( (array) $parents as $parent) {
                $parent = &$this->_getObject('role', $parent);
                $parent['parents'][$role['name']] = &$role;
            }
        }
        
        return $this;       
    }
    
    public function addSubjects($subjects)
    {
        foreach((array) $subjects as $subject) {
            $this->addSubject($subject);
        }
        
        return $this;
    }
    
    public function addSubject($subject)
    {
        $this->_addObject('subject', $subject);
        return $this;
    }
    
    public function getSubjects($method = self::AS_STRING)
    {
        return $this->_getObjects('subject', $method);
    }
    
    public function addRoles($roles)
    {
        foreach( (array) $roles as $role) {
            $this->addRole($role);
        }
        
        return $this;
    }
    
    public function addRole($role)
    {
        $this->_addObject('role', $role);
        return $this;
    }
    
    public function getRoles($method = self::AS_STRING)
    {
        return $this->_getObjects('role', $method);
    }
    
    public function addResources($resources)
    {
        foreach( (array) $resources as $resource) {
            $this->addResource($resource);
        }
        
        return $this;
    }
    
    public function addResource($resource)
    {
        $this->_addObject('resource', $resource);
        return $this;
    }
    
    public function getResources($method = self::AS_STRING)
    {
        return $this->_getObjects('resource', $method);
    }
}
