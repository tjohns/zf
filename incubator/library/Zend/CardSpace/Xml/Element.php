<?php

require_once 'Zend/CardSpace/Xml/Exception.php';
require_once 'Zend/CardSpace/Xml/Element/Interface.php';
require_once 'Zend/Loader.php';

abstract class Zend_CardSpace_Xml_Element extends SimpleXMLElement implements Zend_CardSpace_Xml_Element_Interface {
		
	public function __toString() {
		return $this->asXML();
	}

	static public function convertToDOM(SimpleXMLElement $e) {
		$dom = dom_import_simplexml($e);
		
		if(!($dom instanceof DOMElement)) {
			throw new Zend_CardSpace_Xml_Exception("Failed to convert between SimpleXML and DOM");
		}
		
		return $dom;
	}
	
	static public function convertToObject(DOMElement $e, $classname) {
		
		Zend_Loader::loadClass($classname);
		
		$reflection = new ReflectionClass($classname);
		
		if(!$reflection->isSubclassOf('Zend_CardSpace_Xml_Element')) {
			throw new Zend_CardSpace_Xml_Exception("DOM element must be converted to an instance of Zend_CardSpace_Xml_Element");
		}
		
		$sxe = simplexml_import_dom($e, $classname); 
		
		if(!($sxe instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Failed to convert between DOM and SimpleXML");
		}
		
		return $sxe;
	}
}