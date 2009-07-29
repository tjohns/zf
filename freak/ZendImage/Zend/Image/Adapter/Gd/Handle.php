<?php
class Zend_Image_Adapter_Gd_Handle
    extends Zend_Image_Adapter_Handle_Abstract
{
	protected $_resource;
	
	public function __construct($resource) {
		$this->_resource = $resource;
	}
	
	public function getResource() {
		return $this->_resource;
	}
}