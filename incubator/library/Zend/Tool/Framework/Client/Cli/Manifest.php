<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Filter.php';
require_once 'Zend/Filter/Word/CamelCaseToDash.php';
require_once 'Zend/Filter/StringToLower.php';
require_once 'Zend/Tool/Framework/Manifest/ActionMetadata.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderMetadata.php';

class Zend_Tool_Framework_Client_Cli_Manifest implements Zend_Tool_Framework_Manifest_Interface
{
    
    public function getMetadata()
    {
        $metadatas = array();
        $ccToDashedFilter = new Zend_Filter();
        $ccToDashedFilter->addFilter(new Zend_Filter_Word_CamelCaseToDash())
            ->addFilter(new Zend_Filter_StringToLower());
        
        $clientRegistry = Zend_Tool_Framework_Registry::getInstance();
        $actionRepository   = $clientRegistry->getActionRepository();
        $providerRepository = $clientRegistry->getProviderRepository();
        
        foreach ($actionRepository->getActions() as $action) {
            $metadatas[] = new Zend_Tool_Framework_Manifest_ActionMetadata(array(
                'actionName' => $action->getName(),    
                'name'  => 'cliActionName',
                'value' => $ccToDashedFilter->filter($action->getName()),
                'reference' => $action
                ));
        }

        foreach ($providerRepository->getProviderSignatures() as $providerSignature) {

            $group = $providerSignature->getName();
            $cliProviderName = $ccToDashedFilter->filter($group);

            $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                'providerName' => $providerSignature->getName(),
                'name'  => 'cliProviderName',
                'value' => $cliProviderName,
                'reference' => $providerSignature
                ));

            $providerSignatureSpecialties = $providerSignature->getSpecialties();
            foreach ($providerSignatureSpecialties as $specialty) {
                //$cliSpecialtyNames[$specialty] = $ccToDashedFilter->filter($specialty);
                
                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $specialty,
                    'name'  => 'cliSpecialtyNames',
                    'value' =>  $ccToDashedFilter->filter($specialty)
                    ));                
                
            }

            $cliActionableMethodLongParameters = $cliActionableMethodShortParameters = array();
            $actionableMethods = $providerSignature->getActionableMethods();

            // $actionableMethod is keyed by the methodName (but not used)
            foreach ($actionableMethods as $actionableMethodData) {
                $cliActionableMethodLongParameters = $cliActionableMethodShortParameters = array();
                
                // $actionableMethodData is keyed by $parameterName (But not used)
                foreach ($actionableMethodData['parameterInfo'] as $parameterInfoData) {
                    $cliActionableMethodLongParameters[$parameterInfoData['name']] = $ccToDashedFilter->filter($parameterInfoData['name']);
                    $cliActionableMethodShortParameters[$parameterInfoData['name']] = strtolower($parameterInfoData['name'][0]);
                }

                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName'],
                    'name'  => 'cliActionableMethodLongParameters',
                    'value' => $cliActionableMethodLongParameters,
                    'reference' => &$actionableMethodData
                    ));
                    
                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName'],
                    'name'  => 'cliActionableMethodShortParameters',
                    'value' => $cliActionableMethodShortParameters,
                    'reference' => &$actionableMethodData
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
