<?php

interface Zend_Tool_Endpoint_Inflector_Interface
{
    /**
     * This method will take an action name and format it for Tool usage,
     * The tool will EXPECT this Action Name in CamelCase
     *
     * @param string $actionName
     */
    public function actionNameForTool($actionName);
    
    /**
     * This method will take an Action Name and format it for Endpoint usage,
     * The tool will be PROVIDING the Action Name in CamelCase
     *
     * @param string $actionName
     */
    public function actionNameForEndpoint($actionName);
    
    /**
     * This method will take a Provider Name and format it for Tool usage,
     * The Tool will EXPECT this Provider Name in camelCase
     *
     * @param string $providerName
     */
    public function providerNameForTool($providerName);
    
    /**
     * This method will take a Provider Name and format it for Endpoint usage,
     * The Tool will be PROVIDING the Provier Name in camelCase 
     *
     * @param string $providerName
     */
    public function providerNameForEndpoint($providerName);
    
    /**
     * This method will take a Parameter Name and format it for Tool usage,
     * The Tool will EXPECT this Parameter Name in camelCase
     *
     * @param string $providerName
     */
    public function parameterNameForTool($parameterName);
    
    /**
     * This method will take a Parameter Name and format it for Endpoint usage,
     * The Tool will be PROVIDING the Parameter Name in camelCase 
     *
     * @param string $providerName
     */
    public function parameterNameForEndpoint($parameterName);

}
