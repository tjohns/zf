<?php

class Zend_Tool_Framework_Client_Console_HelpSystem
{
    /**
     * @var Zend_Tool_Framework_Registry_Interface
     */
    protected $_registry = null;
    
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        $this->_registry = $registry;
        return $this;
    }
    
    public function respondWithErrorMessage($errorMessage)
    {
        // get response object
        $response = $this->_registry->getResponse();
        
        // break apart the message into wrapped chunks
        $errorMessages = explode(PHP_EOL, wordwrap($errorMessage, 70, PHP_EOL, false));
        
        $text = '                       An Error Has Occurred                            ';
        $response->appendContent($text, array('color' => array('hiWhite', 'bgRed')));
        
        foreach ($errorMessages as $errorMessage) {
            $errorMessage = sprintf('%-70s', $errorMessage);
            $response->appendContent(' ' . $errorMessage . ' ', array('color' => array('white', 'bgRed')));
        }
        
        $response->appendContent(null, array('seprator' => 2));
        
        

    }
    
    public function respondWithGeneralHelp()
    {
        $response = $this->_registry->getResponse();
        
        /**
        $response->appendContent('Z',        array('color'=>'hiGreen','separator'=>false));
        $response->appendContent('end ',     array('color'=>'hiCyan','separator'=>false));
        $response->appendContent('F',        array('color'=>'hiGreen','separator'=>false));
        $response->appendContent('ramework', array('color'=>'hiCyan','separator'=>false));
        */
        
        $response->appendContent('Zend Framework', array('color' => array('hiWhite'), 'separator' => false));
        $response->appendContent(' Command Line Console Tool v' . Zend_Version::VERSION . '');
        
        $noSeparator = array('separator' => false);
        
        $response->appendContent('Usage:', array('color' => 'green'))
            ->appendContent('    ', $noSeparator)
            ->appendContent('zf', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--global-opts]', $noSeparator)
            ->appendContent(' action-name', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--action-opts]', $noSeparator)
            ->appendContent(' provider-name', array_merge(array('color' => 'cyan'), $noSeparator))
            ->appendContent(' [--provider-opts]', $noSeparator)
            ->appendContent(' [provider parameters ...]')
            ->appendContent('    Note: You may use "?" in any place of the above usage string to ask for more specific help information.', array('color'=>'yellow'))
            ->appendContent('    Example: "zf ? version" will list all available actions for the version provider.', array('color'=>'yellow', 'separator' => 2))
            ->appendContent('Providers and their actions:', array('color' => 'green'));

        $manifest = $this->_registry->getManifestRepository();
        $providerMetadatas = $manifest->getMetadatas(array(
            'type'       => 'Tool',
            'name'       => 'providerName',
            'clientName' => 'console'
            ));

        foreach ($providerMetadatas as $providerMetadata) {
            
            $response->appendContent('  ' . $providerMetadata->getProviderName());
            
            foreach ($providerMetadata->getReference()->getActionableMethods() as $actionableMethod) {
                $actionName = $actionableMethod['action']->getName();
                $actionMetadata = $manifest->getMetadata(array(
                    'type'       => 'Tool',
                    'name'       => 'actionName',
                    'actionName' => $actionName,
                    'clientName' => 'console'
                    ));

                $response->appendContent(
                    '    zf ' . $actionMetadata->getValue() . ' ' . $providerMetadata->getValue(),
                    array('color' => 'cyan') //, 'separator' => false)
                    );
                    
                $parameterMetadata = $manifest->getMetadata($search = array(
                    'type'       => 'Tool',
                    'name'       => 'actionableMethodLongParameters',
                    'providerName' => $providerMetadata->getProviderName(),
                    'actionName' => $actionName,
                    'clientName' => 'console'
                    ));
                   
                var_dump($search);
                var_dump($parameterMetadata);
                    
                //$responseProviderActions[] = $actionMetadata->getValue();
                
                
                
            }
            //$responseProviderActions = array_unique($responseProviderActions);
            //$response->appendContent('    (' . implode('|', $responseProviderActions) . ') ' . $providerMetadata->getValue());
            $response->appendContent(null);
        }
            
    }
    
    public function respondWithProviderHelp($providerName)
    {
        
    }
    
    public function respondWithActionHelp($actionName)
    {
        
    }
    
    
    /**
     * Internal routine for creating help messages
     *
     * @param array $options
     */
//    protected function _createHelpResponse(Array $options = array())
//    {
//        $response = '';
//
//        if (isset($options['error'])) {
//            $response .= $options['error'] . PHP_EOL . PHP_EOL;
//        }
//
//        //$response .= 
//        //          . PHP_EOL;
//
//        $actionName    = $this->_request->getActionName();
//        $providerName  = $this->_request->getProviderName();
//        //$specialtyName = $this->_request->getSpecialtyName();
//                  
//        // both action and provider are known
//        if ($actionName != '' && $actionName != '?' && $providerName != '' && $providerName != '?') {
//            
//            if ($this->_metadataProviderOptionsLong) {
//                $response .= '    Options for this action/resource: ' . PHP_EOL;
//                $optionsReference = $this->_metadataProviderOptionsLong->getReference();
//                $shortNameRef = $this->_metadataProviderOptionsShort->getValue();
//
//                foreach ($this->_metadataProviderOptionsLong->getValue() as $optionName => $consoleOptionName) {
//                    $response .= '       --' . $consoleOptionName . '|-' . $shortNameRef[$optionName];
//                    if ($desc = $optionsReference['parameterInfo'][$optionName]['description']) {
//                        $response .= ' (\033[32m' . $desc . '\033[37m)' . PHP_EOL;
//                    }
//                }
//            } else {
//                $response .= '    There are no options available for this action-provider.';
//            }
//
//        // action name is known, provider is not
//        } elseif ($actionName != '' && $actionName != '?' && ($providerName == '' || $providerName == '?') ) {
//
//            // find all providers this action applies to
//            $providersSupported = array();
//            $providerMetadatas = $this->_registry->getManifestRepository()->getMetadatas(array(
//                'type'       => 'Tool', 
//                'name'       => 'providerName',
//                'clientName' => 'console'
//                ));
//
//            foreach ($providerMetadatas as $providerMetadata) {
//                foreach ($providerMetadata->getReference()->getActionableMethods() as $actionableMethod) {
//                    if ($actionableMethod['actionName'] == $this->_metadataAction->getActionName()) {
//                        $providersSupported[] = $providerMetadata->getValue();
//                    }
//                }
//            }
//
//            $providersSupported = array_unique($providersSupported);
//
//            if ($providersSupported) {
//                $response .= '    Supported providers: ' . PHP_EOL . '    ';
//                $response .= implode(PHP_EOL . '    ', $providersSupported);
//            } else {
//                $response .= '    No supported providers for action ' . $this->_metadataAction->getActionName();
//            }
//
//        // provider name is known, action is not
//        } elseif ($providerName != '' && $providerName != '?' && ($actionName == '' || $actionName == '?')) {
//            
//            //$actionsSupported = array();
//            
//            $validActions = $this->_metadataProvider->getReference()->getActions();
//
//            $response .= '    Supported actions:' . PHP_EOL;
//
//            foreach ($validActions as $validAction) {
//                $actionMetadata = $this->_registry->getManifestRepository()->getMetadata(array(
//                    'type'       => 'Tool',
//                    'name'       => 'actionName',
//                    'actionName' => $validAction->getName(),
//                    'clientName' => 'console'
//                    ));
//
//                $response .= '    ' . $actionMetadata->getValue() . PHP_EOL;
//
//            }
//
//        } else {
//
//
//        }
//
//        $this->_response->appendContent($response);
//        return;
//    }
}
