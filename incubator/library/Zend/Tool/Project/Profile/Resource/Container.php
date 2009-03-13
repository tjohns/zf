<?php

require_once 'Zend/Tool/Project/Profile/Resource/SearchConstraints.php';

class Zend_Tool_Project_Profile_Resource_Container implements RecursiveIterator, Countable
{
    /**
     * @var array
     */
    protected $_subResources = array();
    
    /**
     * @var int
     */
    protected $_position = 0;

    /**
     * @var bool
     */
    protected $_appendable = true;

    /**
     * Finder method to be able to find resources by context name
     * and attributes.  Example usage:
     * 
     * <code>
     * 
     * </code>
     *
     * @param Zend_Tool_Project_Profile_Resource_SearchConstraints|string|array $searchParameters
     * @return Zend_Tool_Project_Profile_Resource
     */
    public function search($searchConstraints)
    {
        if (!$searchConstraints instanceof Zend_Tool_Project_Profile_Resource_SearchConstraints) {
            $searchConstraints = new Zend_Tool_Project_Profile_Resource_SearchConstraints($searchConstraints);
        }
        
        $this->rewind();
        $riIterator = new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST);
        
        $foundResource     = false;
        $currentConstraint = $searchConstraints->getConstraint();
        $foundDepth        = 0;
        
        while ($currentResource = $riIterator->current()) {
            
            // if current depth is less than found depth, end
            if ($riIterator->getDepth() < $foundDepth) {
                break;
            }
            
            if (strtolower($currentResource->getName()) == strtolower($currentConstraint->name)) {

                $paramsMatch = true;
                
                // @todo check to ensure params match (perhaps)
                if (count($currentConstraint->params) > 0) {
                    $currentResourceAttributes = $currentResource->getAttributes();
                    foreach ($currentConstraint->params as $paramName => $paramValue) {
                        if (!isset($currentResourceAttributes[$paramName]) || $currentResourceAttributes[$paramName] != $paramValue) {
                            $paramsMatch = false;
                            break;
                        }
                    }
                }
                
                if ($paramsMatch) {
                    $foundDepth = $riIterator->getDepth();
                    
                    if (($currentConstraint = $searchConstraints->getConstraint()) == null) {
                        $foundResource = $currentResource;
                        break;
                    }
                }
            }
            
            
            $riIterator->next();
        }
        
        return $foundResource;
    }
    
    public function createResourceAt($appendResourceOrSearchConstraints, $context, Array $attributes = array())
    {
        if (!$appendResourceOrSearchConstraints instanceof Zend_Tool_Project_Profile_Resource_Container) {
            if (($parentResource = $this->search($appendResourceOrSearchConstraints)) == false) {
                require_once 'Zend/Tool/Project/Profile/Exception.php';
                throw new Zend_Tool_Project_Profile_Exception('No node was found to append to.');                
            }
        } else {
            $parentResource = $appendResourceOrSearchConstraints;
        }
        
        return $parentResource->createResource($context, $attributes);
    }
    
    /**
     * Method to create a resource
     *
     * @return Zend_Tool_Project_Profile_Resource
     */
    public function createResource($context, Array $attributes = array())
    {
        if (is_string($context)) {
            $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
            if ($contextRegistry->hasContext($context)) {
                $context = $contextRegistry->getContext($context);
            } else {
                require_once 'Zend/Tool/Project/Profile/Exception.php';
                throw new Zend_Tool_Project_Profile_Exception('Context by name ' . $context . ' was not found in the context registry.');  
            }
        } elseif (!$context instanceof Zend_Tool_Project_Context_Interface) {
            require_once 'Zend/Tool/Project/Profile/Exception.php';
            throw new Zend_Tool_Project_Profile_Exception('Context must be of type string or Zend_Tool_Project_Context_Interface.');  
        }
        
        $newResource = new Zend_Tool_Project_Profile_Resource($context);
        
        if ($attributes) {
            $newResource->setAttributes($attributes);
        }
        
        /**
         * Interesting logic here:
         * 
         * First set the parentResource (this will also be done inside append).  This will allow
         * the initialization routine to change the appendability of the parent resource.  This
         * is important to allow specific resources to be appendable by very specific sub-resources. 
         */
        $newResource->setParentResource($this);
        $newResource->initializeContext();
        $this->append($newResource);

        return $newResource;
    }
    
    public function setAttributes(Array $attributes)
    {
        foreach ($attributes as $attrName => $attrValue) {
            $setMethod = 'set' . $attrName;
            if (method_exists($this, $setMethod)) {
                $this->{$setMethod}($attrValue);
            } else {
                $this->setAttribute($attrName, $attrValue);
            }
        }
        return $this;
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }
    
    public function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }
    
    public function getAttribute($name)
    {
        return (array_key_exists($name, $this->_attributes)) ? $this->_attributes[$name] : null;
    }

    public function setAppendable($appendable)
    {
        $this->_appendable = (bool) $appendable;
        return $this;
    }
    
    public function isAppendable()
    {
        return $this->_appendable;
    }
    
    public function setParentResource(Zend_Tool_Project_Profile_Resource_Container $parentResource)
    {
        $this->_parentResource = $parentResource;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return Zend_Tool_Project_Profile_Resource_Container
     */
    public function getParentResource()
    {
        return $this->_parentResource;
    }
    
    public function append(Zend_Tool_Project_Profile_Resource_Container $resource)
    {
        if (!$this->isAppendable()) {
            throw new Exception('Resource by name ' . (string) $this . ' is not appendable');
        }
        array_push($this->_subResources, $resource);
        $resource->setParentResource($this);
        
        return $this;
    }
    
    public function current()
    {
        return current($this->_subResources);
    }
    
    public function key()
    {
        return key($this->_subResources);
    }
    
    public function next()
    {
        return next($this->_subResources);
    }
    
    public function rewind()
    {
        return reset($this->_subResources);
    }
    
    public function valid()
    {
        return (bool) $this->current();
    }
    
    public function hasChildren()
    {
        return (count($this->_subResources > 0)) ? true : false;
    }
    
    public function getChildren()
    {
        return $this->current();
    }
    
    public function count()
    {
        return count($this->_subResources);
    }
    
    public function __clone()
    {
        $this->rewind();
        foreach ($this->_subResources as $index => $resource) {
            $this->_subResources[$index] = clone $resource;
        }
    }
    
}