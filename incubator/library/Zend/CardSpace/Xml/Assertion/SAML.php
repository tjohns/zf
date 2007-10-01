<?php

require_once 'Zend/CardSpace/Xml/Element.php';
require_once 'Zend/CardSpace/Xml/Assertion/Interface.php';

class Zend_CardSpace_Xml_Assertion_SAML extends Zend_CardSpace_Xml_Element
                                        implements Zend_CardSpace_Xml_Assertion_Interface {
	
	const CONDITION_AUDIENCE = 'AudienceRestrictionCondition';
	const CONFIRMATION_BEARER = 'urn:oasis:names:tc:SAML:1.0:cm:bearer';
	const CONDITION_TIME_ADJ = 3600; // +- 5 minutes
	
	public function validateConditions(Array $conditions) {
		
		$currentTime = time();

		if(!empty($conditions)) {
			foreach($conditions as $condition => $conditionValue) {
				switch(strtolower($condition)) {
					case 'notbefore':
						$notbeforetime = strtotime($conditionValue);
						if(($currentTime < $notbeforetime) &&
						   ($currentTime + self::CONDITION_TIME_ADJ < $notbeforetime)) {
							return array($condition, 'Current time is before specified window');
						   }
						break;
					case 'notonorafter':
						$notonoraftertime = strtotime($conditionValue);

						if(($currentTime >= $notonoraftertime) &&
						   ($currentTime - self::CONDITION_TIME_ADJ >= $notonoraftertime)) {
							return array($condition, 'Current time is after specified window');
						}
						break;
					case 'audience':
						
						$self_aliases = array("https://{$_SERVER['SERVER_NAME']}/",
						                      "https://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}");
						$found = false;
				                      
						if(is_array($conditionValue)) {
							foreach($conditionValue as $audience) {
								if(in_array($audience, $self_aliases)) {
									$found = true;
									break;
								}
							}
						}
						
						if(!$found) {
							return array($condition, 'Could not find self in allowed audience list');
						}
						
						break;
				}
			}
		}
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