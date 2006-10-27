<?

/**
 * Zend_Session_Validator_Interface
 *
 */
require_once 'Zend/Session/Validator/Interface.php';


/**
 * Zend_Session_Validator_Abstract
 *
 */
abstract class Zend_Session_Validator_Abstract implements Zend_Session_Validator_Interface
{
    
    /**
     * SetValidData() - This method should be used to store the environment variables that
     * will be needed in order to validate the session later in the validate() method.
     * These values are stored in the session in the __ZF namespace, in an array named VALID
     *
     * @param mixed $data
     */
    protected function setValidData($data)
    {
        $validator_name = get_class($this);
        
        $_SESSION['__ZF']['VALID'][$validator_name] = $data;
    }
    
    
    /**
     * GetValidData() - This method should be used to retrieve the environment variables that 
     * will be needed to 'validate' a session.
     *
     * @return mixed
     */
    protected function getValidData()
    {
        $validator_name = get_class($this);
        
        return $_SESSION['__ZF']['VALID'][$validator_name];
    }

}