<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Filter.php';
require_once 'Zend/Filter/Word/CamelCaseToDash.php';
require_once 'Zend/Filter/StringToLower.php';
require_once 'Zend/Tool/Framework/Manifest/ActionMetadata.php';
require_once 'Zend/Tool/Framework/Manifest/ProviderMetadata.php';

require_once 'Zend/Tool/Framework/Registry/EnabledInterface.php';

/**
 * Zend_Tool_Framework_Client_ConsoleClient_Manifest
 *
 */
class Zend_Tool_Framework_Client_ConsoleClient_Manifest 
    implements Zend_Tool_Framework_Manifest_Interface, Zend_Tool_Framework_Registry_EnabledInterface
{
    
    /**
     * @var Zend_Tool_Framework_Registry_Interface
     */
    protected $_registry = null;
    
    /**
     * setRegistry() - Required for the Zend_Tool_Framework_Registry_EnabledInterface interface
     *
     * @param Zend_Tool_Framework_Registry_Interface $registry
     * @return Zend_Tool_Framework_Client_ConsoleClient_Manifest
     */
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
        return $this;
    }
    
    /**
     * getMetadata() is required by the Manifest Interface.
     * 
     * These are the following metadatas that will be setup:
     * 
     * cliActionName
     *   - metadata for actions
     *   - value will be a dashed name for the action named in 'actionName'
     * cliProviderName
     *   - metadata for providers
     *   - value will be a dashed-name for the provider named in 'providerName'
     * cliSpecialtyNames
     *   - metadata for providers
     * cliActionableMethodLongParameters
     *   - metadata for providers
     * cliActionableMethodShortParameters
     *   - metadata for providers
     *
     * @return unknown
     */
    public function getMetadata()
    {
        $metadatas = array();
        
        // setup the camelCase to dashed filter to use since cli expects dashed named
        $ccToDashedFilter = new Zend_Filter();
        $ccToDashedFilter->addFilter(new Zend_Filter_Word_CamelCaseToDash())
            ->addFilter(new Zend_Filter_StringToLower());
        
        // get the registry to get the action and provider repository
        $actionRepository   = $this->_registry->getActionRepository();
        $providerRepository = $this->_registry->getProviderRepository();
        
        // loop through all actions and create a metadata for each
        foreach ($actionRepository->getActions() as $action) {
            // each action metadata will be called
            $metadatas[] = new Zend_Tool_Framework_Manifest_ActionMetadata(array(
                'name'  => 'cliActionName',
                'value' => $ccToDashedFilter->filter($action->getName()),
                'reference' => $action,
                'actionName' => $action->getName()
                ));
        }

        foreach ($providerRepository->getProviderSignatures() as $providerSignature) {

            $group = $providerSignature->getName();
            $cliProviderName = $ccToDashedFilter->filter($group);

            // create the metadata for the provider's cliProviderName
            $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                'name'  => 'cliProviderName',
                'value' => $cliProviderName,
                'reference' => $providerSignature,
                'providerName' => $providerSignature->getName()
                ));

            // create the metadatas for the per provider specialites in cliSpecaltyNames
            $providerSignatureSpecialties = $providerSignature->getSpecialties();
            foreach ($providerSignatureSpecialties as $specialty) {
                
                //$cliSpecialtyNames[$specialty] = $ccToDashedFilter->filter($specialty);
                
                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'name'  => 'cliSpecialtyNames',
                    'value' =>  $ccToDashedFilter->filter($specialty),
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $specialty,
                    ));                
                
            }

            $cliActionableMethodLongParameters = $cliActionableMethodShortParameters = array();
            $actionableMethods = $providerSignature->getActionableMethods();

            // $actionableMethod is keyed by the methodName (but not used)
            foreach ($actionableMethods as $actionableMethodData) {
                $cliActionableMethodLongParameters = $cliActionableMethodShortParameters = array();
                
                // $actionableMethodData get both the long and short names
                foreach ($actionableMethodData['parameterInfo'] as $parameterInfoData) {
                    // filter to dashed
                    $cliActionableMethodLongParameters[$parameterInfoData['name']] = $ccToDashedFilter->filter($parameterInfoData['name']);
                    // simply lower the character, (its only 1 char after all)
                    $cliActionableMethodShortParameters[$parameterInfoData['name']] = strtolower($parameterInfoData['name'][0]);
                }

                // create metadata for the long name cliActionableMethodLongParameters
                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'name'  => 'cliActionableMethodLongParameters',
                    'value' => $cliActionableMethodLongParameters,
                    'reference' => &$actionableMethodData,
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName']
                    ));
                    
                // create metadata for the short name cliActionableMethodShortParameters
                $metadatas[] = new Zend_Tool_Framework_Manifest_ProviderMetadata(array(
                    'name'  => 'cliActionableMethodShortParameters',
                    'value' => $cliActionableMethodShortParameters,
                    'reference' => &$actionableMethodData,
                    'providerName' => $providerSignature->getName(),
                    'specialtyName' => $actionableMethodData['specialty'],
                    'actionName' => $actionableMethodData['actionName'],
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
