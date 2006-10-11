<?php
interface Zend_Request_Interface
{
    /**
     * Overloading for accessing class property values
     * 
     * @param string $key 
     * @return mixed
     */
    public function __get($key);

    /**
     * Overloading for setting class property values
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void
     */
    public function __set($key, $value);

    /**
     * Overloading to determine if a property is set
     * 
     * @param string $key 
     * @return boolean
     */
    public function __isset($key);

    /**
     * Alias for __get()
     * 
     * @param string $key 
     * @return mixed
     */
    public function get($key);

    /**
     * Alias for __set()
     * 
     * @param string $key 
     * @param mixed $value 
     * @return void
     */
    public function set($key, $value);

    /**
     * Alias for __isset()
     * 
     * @param string $key 
     * @return boolean
     */
    public function has();

    /**
     * Either alias for __get(), or provides ability to maintain separate 
     * configuration registry for request object.
     * 
     * @param string $key 
     * @return mixed
     */
    public function getParam($key);

    /**
     * Either alias for __set(), or provides ability to maintain separate 
     * configuration registry for request object.
     * 
     * @param string $key 
     * @param mixed $value
     * @return void
     */
    public function setParam($key, $value);

    /**
     * Get all params handled by get/setParam()
     * 
     * @return array
     */
    public function getParams();

    /**
     * Set all values handled by get/setParam()
     * 
     * @param array $params 
     * @return void
     */
    public function setParams($params);
}
