<?php
require_once 'Zend/Rbac/Subject.php';

class Zend_Rbac
{
	const AS_OBJECT = 'AS_OBJECT';
	const AS_STRING = 'AS_STRING';
	
	protected $_subjects = array();
	
	protected $_strictMode = false;
	
	public function __construct($options = null) {
        $this->addOptions($options);
	}
	
	public function addOptions($options) {
		$options = array_change_key_case($options);
        foreach($options as $key => $value) {
        	switch($key) {
        		case 'subject':
        		case 'subjects':
                    $this->addSubjects($value);
                    break;
                /// 
        	}
        }
	}
	
	public function addSubjects($subjects) {
		foreach((array) $subjects as $subject) {
			$this->addSubject($subject);
		}
		
		return $this;
	}
	
	public function addSubject($subject) {
        if($subject instanceof Zend_Rbac_Subject) {
            if($this->_strictMode && $this->isSubjectRegistered($subject)) {
                throw new Zend_Rbac_Exception(
                    "Cannot add subject with name {$subject} twice"
                );
            }
            
            $this->_subjects[] = $subject;
            return $this;
        } elseif(is_string($subject) ||
		        (is_object($subject) && is_callable(array($subject, '__toString'))))
		{
			if($this->_strictMode && $this->isSubjectRegistered($subject)) {
				throw new Zend_Rbac_Exception(
				    "Cannot add subject with name {$subject} twice"
				);
			}
            
			$this->_subjects[] = new Zend_Rbac_Subject((string)$subject);
			return $this;
		}
		
		throw new Zend_Rbac_Exception('Invalid subject supplied');
	}
	
	public function getSubjects($method = 'AS_STRING')
	{
	   if($method == self::AS_STRING) {
            $out = array();
            foreach($this->_subjects as $subject) {
                $out[] = $subject->__toString();
	       }
	       
	       return $out;
	    }

	    if($method == self::AS_OBJECT) {
	       return $this->_subjects;
        }
	}
}
