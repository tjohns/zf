<?php

/**
 * Zend_Session_Validator_Abstract
 *
 */
require_once 'Zend/Session/Validator/Abstract.php';


/**
 * Zend_Session_Validator_HttpUserAgent
 *
 */
class Zend_Session_Validator_HttpUserAgent extends Zend_Session_Validator_Abstract 
{
    
    /**
     * Setup() - this method will get the current user agent and store it in the session
     * as 'valid data'
     *
     */
    public function setup()
    {
        $this->setValidData($_SERVER['HTTP_USER_AGENT']);
    }
    
    /**
     * Validate() - this method will determine if the current user agent matches the 
     * user agent we stored when we initialized this variable.
     *
     * @return bool
     */
    public function validate()
    {
        $current_browser = $_SERVER['HTTP_USER_AGENT'];
        
        if ($current_browser === $this->getValidData()) {
            return true;
        } else {
            return false;
        }
    }
    
}
