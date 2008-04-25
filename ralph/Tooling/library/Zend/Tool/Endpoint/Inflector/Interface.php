<?php

interface Zend_Tool_Endpoint_Inflector_Interface
{
    
    public function actionNameForTool($actionName);
    public function actionNameForEndpoint($actionName);
    public function providerNameForTool($providerName);
    public function providerNameForEndpoint($providerName);
    public function parameterNameForTool($parameterName);
    public function parameterNameForEndpoint($parameterName);

}
