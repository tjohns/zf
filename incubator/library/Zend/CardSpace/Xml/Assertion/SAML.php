<?php

require_once 'Zend/CardSpace/Xml/Element.php';
require_once 'Zend/CardSpace/Xml/Assertion.php';

class Zend_CardSpace_Xml_Assertion_SAML extends Zend_CardSpace_Xml_Element
                                        implements Zend_CardSpace_Xml_Assertion_Interface {
	
	const CONDITION_AUDIENCE = 'AudienceRestrictionCondition';
	const CONFIRMATION_BEARER = 'urn:oasis:names:tc:SAML:1.0:cm:bearer';

	public function validateConditions($conditions) {
		return true;	
	}
	
	public function getAssertionURI() {
		return Zend_CardSpace_Xml_Assertion::TYPE_SAML;
	}
	
	public function getMajorVersion() {
		return (string)$this['MajorVersion'];
	}
	
	public function getMinorVersion() {
		return (string)$this['MinorVersion'];
	}
	
	public function getAssertionID() {
		return (string)$this['AssertionID'];
	}
	
	public function getIssuer() {
		return (string)$this['Issuer'];
	}
	
	public function getIssuedTimestamp() {
		return strtotime((string)$this['IssueInstant']);
	}
	
	public function getConditions() {
		
		list($conditions) = $this->xpath("//saml:Conditions");

		if(!($conditions instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the saml:Conditions block");
		}

		$retval = array();

		$retval['NotBefore'] = (string)$conditions['NotBefore'];
		$retval['NotOnOrAfter'] = (string)$conditions['NotOnOrAfter'];
		
		foreach($conditions->children('urn:oasis:names:tc:SAML:1.0:assertion') as $key => $value) {
			switch($key) {
				case self::CONDITION_AUDIENCE:
					foreach($value->children('urn:oasis:names:tc:SAML:1.0:assertion') as $audience_key => $audience_value) {
						if($audience_key == 'Audience') {
							$retval[$key][] = (string)$audience_value;
						}
					}
					break;
			}
		}
		
		return $retval;
	}
	
	public function getSubjectKeyInfo() {
		/**
		 * @todo Not sure if this is part of the scope for now..
		 */
		
		if($this->getConfirmationMethod() == self::CONFIRMATION_BEARER) {
			throw new Zend_CardSpace_Xml_Exception("Cannot get Subject Key Info when Confirmation Method was Bearer");
		}
	}
	
	public function getConfirmationMethod() {
		list($confirmation) = $this->xPath("//saml:ConfirmationMethod");
		return (string)$confirmation;
	}
	
	public function getAttributes() {
		$attributes = $this->xPath('//saml:Attribute');
		
		$retval = array();
		foreach($attributes as $key => $value) {
			
			$retkey = (string)$value['AttributeNamespace'].'/'.(string)$value['AttributeName']; 
			$retval[$retkey] = array('name' => (string)$value['AttributeName'],
			                         'namespace' => (string)$value['AttributeNamespace']);
		
			list($aValue) = $value->children('urn:oasis:names:tc:SAML:1.0:assertion');
			$retval[$retkey]['value'] = (string)$aValue;
		}
		
		return $retval;
	}
	
	public function getSignature() {
		
	}
}