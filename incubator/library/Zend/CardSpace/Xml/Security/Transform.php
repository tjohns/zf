<?php

require_once 'Zend/Loader.php';

class Zend_CardSpace_Xml_Security_Transform {
	
	protected $_transformList = array();
	
	protected function findClassbyURI($uri) {
		switch($uri) {
			case 'http://www.w3.org/2000/09/xmldsig#enveloped-signature':
				return 'Zend_CardSpace_Xml_Security_Transform_EnvelopedSignature';
			case 'http://www.w3.org/2001/10/xml-exc-c14n#':
				return 'Zend_CardSpace_Xml_Security_Transform_XmlExcC14N';
			default:
				throw new Zend_CardSpace_Xml_Security_Exception("Unknown or Unsupported Transformation Requested");
		}
	}
	
	public function addTransform($uri) {
		
		$class = $this->findClassbyURI($uri);
		
		$this->_transformList[] = array('uri' => $uri,
		                                'class' => $class);
	}
	
	public function removeTransform($id) {
		if(isset($this->_transformList[$id])) {
			unset($this->_transformList[$id]);
			return $this;
		} 
		
		throw new Zend_CardSpace_Xml_Security_Exception("Unknown Transform ID");
	}
	
	public function getTransformList() {
		return $this->_transformList;
	}
	
	public function applyTransforms($strXmlDocument) {
		foreach($this->_transformList as $transform) {
			Zend_Loader::loadClass($transform['class']);			
			
			$transformer = new $transform['class'];
			
			if(!($transformer instanceof Zend_CardSpace_Xml_Security_Transform_Interface)) {
				throw new Zend_CardSpace_Xml_Security_Exception("Transforms must implement the Transform Interface");
			}
			
			$strXmlDocument = $transformer->transform($strXmlDocument);
		}
		
		return $strXmlDocument;
	}
}