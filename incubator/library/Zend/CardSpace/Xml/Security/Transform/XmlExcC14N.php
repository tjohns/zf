<?php

require_once 'Zend/CardSpace/Xml/Security/Transform/Interface.php';
require_once 'Zend/CardSpace/Xml/Security/Transform/Exception.php';

class Zend_CardSpace_Xml_Security_Transform_XmlExcC14N implements Zend_CardSpace_Xml_Security_Transform_Interface {
	
	public function transform($strXMLData) {
		
		$dom = new DOMDocument();
		$dom->loadXML($strXMLData);

		if(method_exists($dom, 'C14N')) {
			return $dom->C14N(true);
		}
		
		throw new Zend_CardSpace_Xml_Security_Transform_Exception("This transform requires the C14N() method to exist in the DOM extension");
	}
}