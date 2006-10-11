<?php
interface Zend_Controller_Request_Interface
{
    /**
     * Retrieve the controller name
     * 
     * @return string
     */
    public function getControllerName();

    /**
     * Set the controller name to use
     * 
     * @param string $value 
     * @return void
     */
    public function setControllerName($value);

    /**
     * Retrieve the action name
     * 
     * @return string
     */
    public function getActionName();

    /**
     * Set the action name 
     * 
     * @param string $value 
     * @return void
     */
    public function setActionName($value);

    /**
     * Get an action parameter
     * 
     * @param string $key 
     * @return mixed
     */
    public function getParam($key);

    /**
     * Set an action parameter
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void
     */
    public function setParam($key, $value);

    /**
     * Get all action parameters
     * 
     * @return array
     */
    public function getParams();

    /**
     * Set action parameters en masse; does not overwrite
     * 
     * @param array $array 
     * @return void
     */
    public function setParams($array);
}
