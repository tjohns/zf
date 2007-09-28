<?php

require_once 'Zend/CardSpace/Xml/Security/Transform/Interface.php';
require_once 'Zend/CardSpace/Xml/Security/Transform/Exception.php';

class Zend_CardSpace_Xml_Security_Transform_EnvelopedSignature implements Zend_CardSpace_Xml_Security_Transform_Interface {
	
	public function transform($strXMLData) {
		
		$sxe = simplexml_load_string($strXMLData);
		
		if(!$sxe->Signature) {
			throw new Zend_CardSpace_Xml_Security_Transform_Exception("Unable to locate Signature Block for EnvelopedSignature Transform");
		}
		
		unset($sxe->Signature);
		
		return $sxe->asXML();
	}
}