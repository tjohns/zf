<?php
/**
 * @todo Evaluate if this file is the correct place for this
 */
abstract class Zend_Image_Adapter_Handle_Abstract
{
   protected $_resource;
   
    protected $_width;
    protected $_height;
    protected $_type;
    protected $_bits;
    protected $_channels;
    
    public function __construct($resource) {
        $this->_resource = $resource;
    }
    
    public function getResource() {
        return $this->_resource;
    }
    
    public function setInfo(array $params) {
    	foreach($params as $key => $value) {
    		$this->{'_'.$key} = $value;
    	}
    	var_dump($this); exit;
    }
}
