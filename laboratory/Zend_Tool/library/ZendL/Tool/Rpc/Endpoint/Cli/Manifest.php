<?php

class ZendL_Tool_Rpc_Endpoint_Cli_Manifest implements ZendL_Tool_Rpc_Manifest_Interface
{
    
    public function getMetadata()
    {
        $metadatas = array();
        $ccToDashedFilter = new Zend_Filter();
        $ccToDashedFilter->addFilter(new Zend_Filter_Word_CamelCaseToDash())
            ->addFilter(new Zend_Filter_StringToLower());
        
        $providerRegistry = ZendL_Tool_Rpc_Provider_Registry::getInstance();
        
        $cliActionNames = array();
        
        foreach ($providerRegistry->getActions() as $action) {
            $metadatas[] = new ZendL_Tool_Rpc_Manifest_ActionMetadata(array(
                'actionName' => $action->getName(),    
                'name'  => 'cliActionName',
                'value' => $ccToDashedFilter->filter($action->getName()),
                'reference' => $action
                ));
        }

        foreach ($providerRegistry->getProviderSignatures() as $providerSignature) {

            $group = $providerSignature->getName();
            $cliProviderName = $ccToDashedFilter->filter($group);

            $metadatas[] = new ZendL_Tool_Rpc_Manifest_ProviderMetadata(array(
                'providerName' => $providerSignature->getName(),
                'name'  => 'cliProviderName',
                'value' => $cliProviderName,
                'reference' => $providerSignature
                ));

            $cliSpecialtyNames = array();

            $providerSignatureSpecialties = $providerSignature->getSpecialties();
            foreach ($providerSignatureSpecialties as $specialty) {
                //$cliSpecialtyNames[$specialty] = $ccToDashedFilter->filter($specialty);
                
                $metadatas[] = new ZendL_Tool_Rpc_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $specialty,
                    'name'  => 'cliSpecialtyNames',
                    'value' =>  $ccToDashedFilter->filter($specialty)
                    ));                
                
            }

            $cliActionableMethodLongParameters = $cliActionableMethodShortParameters = array();
            $actionableMethods = $providerSignature->getActionableMethods();

            foreach ($actionableMethods as $methodName => $actionableMethodData) {
                foreach ($actionableMethodData['parameterInfo'] as $parameterName => $parameterInfoData) {
                    $cliActionableMethodLongParameters[$parameterInfoData['name']] = $ccToDashedFilter->filter($parameterInfoData['name']);
                    $cliActionableMethodShortParameters[$parameterInfoData['name']] = strtolower($parameterInfoData['name'][0]);
                }

                $metadatas[] = new ZendL_Tool_Rpc_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName'],
                    'name'  => 'cliActionableMethodLongParameters',
                    'value' => $cliActionableMethodLongParameters,
                    'reference' => &$parameterInfoData
                    ));
                    
                $metadatas[] = new ZendL_Tool_Rpc_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName'],
                    'name'  => 'cliActionableMethodShortParameters',
                    'value' => $cliActionableMethodShortParameters,
                    'reference' => &$parameterInfoData
                    ));

            }



        }
        
        return $metadatas;
    }
    
    public function getIndex()
    {
        return 10000;
    }
    
}
