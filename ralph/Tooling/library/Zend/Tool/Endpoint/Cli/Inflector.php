<?php

class Zend_Tool_Endpoint_Cli_Inflector implements Zend_Tool_Endpoint_Inflector_Interface
{
    /**
     * @var Zend_Filter_Word_DashToCamelCase
     */
    protected $_dashedLowerToCamelCase = null;
    
    /**
     * @var Zend_Filter_Word_CamelCaseToDash
     */
    protected $_camelCaseToDashed = null;
    
    public function __construct()
    {
        $this->_dashedLowerToCamelCase = new Zend_Filter_Word_DashToCamelCase();
        $this->_camelCaseToDashed      = new Zend_Filter_Word_CamelCaseToDash();
    }
    
    /**
     * This method will take an action name and format it for Tool usage,
     * The tool will EXPECT this Action Name in CamelCase 
     * 
     * The Cli Endpoint will PROVIDE this action name in 'dashed-lower-format'
     *
     * @param string $actionName
     */
    public function actionNameForTool($actionName)
    {
        return $this->_dashedLowerToCamelCase->filter($actionName);
    }
    
    /**
     * This method will take an Action Name and format it for Endpoint usage,
     * The tool will be PROVIDING the Action Name in CamelCase
     *
     * The Cli Endpoint will EXPECT this Action Name in 'dashed-lower-format'
     * 
     * @param string $actionName
     */
    public function actionNameForEndpoint($actionName)
    {
        return $this->_camelCaseToDashed->filter($actionName);
    }
    
    /**
     * This method will take a Provider Name and format it for Tool usage,
     * The Tool will EXPECT this Provider Name in camelCase
     *
     * The Cli Endpoint will PROVIDE this provider name in 'dashed-lower-format'
     * 
     * @param string $providerName
     */
    public function providerNameForTool($providerName)
    {
        return $this->_dashedLowerToCamelCase->filter($providerName);
    }
    
    /**
     * This method will take a Provider Name and format it for Endpoint usage,
     * The Tool will be PROVIDING the Provier Name in camelCase 
     *
     * The Cli Endpoint will EXPECT the provider name in 'dashed-lower-format'
     * 
     * @param string $providerName
     */
    public function providerNameForEndpoint($providerName)
    {
        return $this->_camelCaseToDashed->filter($providerName);
    }
    
    /**
     * This method will take a Parameter Name and format it for Tool usage,
     * The Tool will EXPECT this Parameter Name in camelCase
     *
     * The Cli Endpoint will PROVIDE the parameter name in 'dashed-lower-format'
     * 
     * @param string $parameterName
     */
    public function parameterNameForTool($parameterName)
    {
        $value = $this->_dashedLowerToCamelCase->filter($parameterName);
        return strtolower($value[0]) . substr($value, 1);
    }

    
    /**
     * This method will take a Parameter Name and format it for Endpoint usage,
     * The Tool will be PROVIDING the Parameter Name in camelCase 
     *
     * The Cli Endpoint will EXPECT the parameter name in 'dashed-lower-format'
     * 
     * @param string $parameterName
     */
    public function parameterNameForEndpoint($parameterName)
    {
        return $this->_camelCaseToDashed->filter($parameterName);
    }
    
}
