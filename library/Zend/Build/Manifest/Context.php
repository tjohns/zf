<?php

class Zend_Build_Manifest_Context
{

	protected $_type = null;
	protected $_name = null;
	protected $_className = null;
	protected $_attributes = null;
	
	public function __construct($type, $name, $className, $attributeList = array())
	{
	    $this->setType($type);
	    $this->_type = $type;
	    $this->_name = $name;
	    $this->_className = $className;
	    $this->_attributes = $attributeList;
	}

    /**
     * @param string $_type
     */
    public function setType($_type)
    {
        $this->_type = $_type;
    }
	
    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $_name
     */
    public function setName($name) {
        $this->_name = $name;
    }
    
    /**
     * @return string
     */
    public function getName() 
    {
        return $this->_name;
    }
    
    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->_className;
    }
    
    /**
     * @param string $_class
     */
    public function setClassName($className)
    {
        $this->_class = $className;
    }
    
    /**
     * @return array
     */
    public function getAttributes() 
    {
        return $this->_attributes;
    }
    
    /**
     * @param array $_attributes
     */
    public function setAttributes(Array $attributes) 
    {
        $this->_attributes = $attributes;
    }
    

    

    


	
	/**
	 * fromXmlFile() - This method will take an xml file and create an array of Context objects
	 *
	 * Expected format for an xml file:
	 * <?xml version="1.0"?>
     * <contexts>
     *   <context type='action' name = 'create' alias = 'c' class='Zend_Build_ClassName'>
     *     <attribute getopt='help|h' otherAttribute="someotherOptions">
     *       <usage>
     *         This is how you use apple with create.
     *       </usage>
     *     </attribute>
     *     ...
     *   </context>
     *   ..
     * </contexts>
	 * 
	 * @param string $xmlFilePath
	 * @return array
	 */
	public static function fromXmlFile($xmlFilePath)
	{
	    $manifestContexts = array();
	    
		$contexts = simplexml_load_file($xmlFilePath);
		
		foreach ($contexts as $context => $contextInfo) {
		    
		    if ($context != 'context') {
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception('Manifest must contain a contexts.');		        
		    }

            if (!isset($contextInfo->attributes()->type)) {
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception('Manifest must contain a type attribute in each context.');
            }
            
            if (!isset($contextInfo->attributes()->name)) {
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception('Manifest must contain a name attribute in each context.');
            }
            
            if (!isset($contextInfo->attributes()->class)) {
                require_once 'Zend/Build/Exception.php';
                throw new Zend_Build_Exception('Manifest must contain a class attribute in each context.');
            }
            
            $attributeList = array();

            foreach ($contextInfo->attribute as $attribute) {

                $attributeItem = array();
                
                foreach ($attribute->attributes() as $attrName => $attrValue) {
                    $attributeItem['attributes'][$attrName] = (string) $attrValue;
                }
                
                $attributeItem['usage'] = trim($attribute->usage); // trim casts to string
                
                $attributeList[] = $attributeItem;
            }

            $manifestContexts[] = new self(
                (string) $contextInfo->attributes()->type, 
                (string) $contextInfo->attributes()->name, 
                (string) $contextInfo->attributes()->class, 
                $attributeList
                );
            
		}
		
        return $manifestContexts;
	}
	
	
}
