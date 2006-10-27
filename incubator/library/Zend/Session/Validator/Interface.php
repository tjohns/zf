<?

/**
 * Zend_Session_Validator_Interface
 *
 */
interface Zend_Session_Validator_Interface
{

    /**
     * Setup() - this method will store the environment variables
     * nessissary to be able to validate against in future requests.
     */
    public function setup();
    
    /**
     * Validate() - this method will be called at the beginning of
     * every session to determine if the current environment matches
     * that which was store in the setup() procedure.
     */
    public function validate();

}