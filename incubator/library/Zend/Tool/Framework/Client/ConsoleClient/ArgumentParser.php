<?php

require_once 'Zend/Console/Getopt.php';

/**
 * The main
 *
 */
class Zend_Tool_Framework_Client_ConsoleClient_ArgumentParser
{
    
    /**
     * @var Zend_Tool_Framework_Registry_Interface
     */
    protected $_registry = null;

    /**
     * @var Zend_Tool_Framework_Client_Request
     */
    protected $_request = null;

    /**
     * @var Zend_Tool_Framework_Client_Response
     */
    protected $_response = null;

    /**#@+
     * @var array
     */
    protected $_argumentsOriginal = null;
    protected $_argumentsWorking  = null;
    /**#@-*/

    /**#@+
     * @var bool
     */
    protected $_help     = false;
    protected $_verbose  = false;
    /**#@-*/

    protected $_metadataAction               = null;
    protected $_metadataProvider             = null;
    protected $_metadataSpecialty            = null;
    protected $_metadataProviderOptionsLong  = null;
    protected $_metadataProviderOptionsShort = null;

    /**
     * Constructor, takes in an client request and response, as well as the arguments.
     *
     * @param array $arguments
     * @param Zend_Tool_Framework_Client_Request $clientRequest
     * @param Zend_Tool_Framework_Client_Response $clientResponse
     */
    public function __construct(Array $arguments, Zend_Tool_Framework_Registry_Interface $registry)
    {
        // set the arguments
        $this->_argumentsOriginal = $this->_argumentsWorking = $arguments;
        
        // get the client registry
        $this->_registry = $registry;
        
        // set manifest repository, request, response for easy access
        $this->_manifestRepository = $this->_registry->getManifestRepository();
        $this->_request      = $this->_registry->getRequest();
        $this->_response     = $this->_registry->getResponse();
        
        if ($this->_request == null || $this->_response == null) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('The client registry must have both a request and response registered.');
        }
    }

    /**
     * Parse() - This method does the work of parsing the arguments into the enpooint request,
     * this will also (during help operations) fill the response in with information as needed
     *
     * @return null
     */
    public function parse()
    {

        // check to see if the first cli arg is the script name
        if ($this->_argumentsWorking[0] == $_SERVER["SCRIPT_NAME"]) {
            array_shift($this->_argumentsWorking);
        }

        // process global options
        $this->_parseGlobalPart();

        // @todo illogical
        if (count($this->_argumentsWorking) == 0) {
            $this->_request->setDispatchable(false); // at this point request is not dispatchable
            if ($this->_help) {
                $this->_createHelpResponse();
            } else {
                $this->_createHelpResponse(array('error' => 'An action and provider is required.'));
            }
            return;
        }

        // process the action part of the command line
        $this->_parseActionPart();

        /* @TODO Action Parameter Requirements */

        // make sure there are more "words" on the command line
        if (count($this->_argumentsWorking) == 0) {
            $this->_request->setDispatchable(false); // at this point request is not dispatchable
            if ($this->_help) {
                $this->_createHelpResponse();
            } else {
                $this->_createHelpResponse(array('error' => 'A provider is required.'));
            }
            return;
        }

        // process the provider part of the command line
        $this->_parseProviderPart();

        // if there are arguments on the command line, lets process them as provider options
        if (count($this->_argumentsWorking) != 0) {
            $this->_parseProviderOptionsPart();
        }

        // if there is still arguments lingering around, we can assume something is wrong
        if (count($this->_argumentsWorking) != 0) {
            $this->_request->setDispatchable(false); // at this point request is not dispatchable
            $this->_createHelpResponse(array(
                'error' => 'Unknown arguments left on the command line: ' . implode(' ', $this->_argumentsWorking))
                );
            return;
        }

        // everything was processed and this is a request for help information
        if ($this->_help) {
            $this->_request->setDispatchable(false); // at this point request is not dispatchable
            $this->_createHelpResponse();
        }

        return;
    }

    /**
     * Internal routine for parsing global options from the command line
     *
     * @return null
     */
    protected function _parseGlobalPart()
    {
        $getoptOptions = array();
        $getoptOptions['help|h']    = 'HELP';
        $getoptOptions['verbose|v'] = 'VERBOSE';
        $getoptOptions['pretend|p'] = 'PRETEND';
        $getoptOptions['debug|d']   = 'DEBUG';
        $getoptParser = new Zend_Console_Getopt($getoptOptions, $this->_argumentsWorking, array('parseAll' => false));
        $getoptParser->parse();

        foreach ($getoptParser->getOptions() as $option) {
            if ($option == 'pretend') {
                $this->_request->setPretend(true);
            } else {
                $property = '_'.$option;
                $this->{$property} = true;                
            }
        }

        $this->_argumentsWorking = $getoptParser->getRemainingArgs();

        return;
    }

    /**
     * Internal routine for parsing the action name from the arguments
     *
     * @return null
     */
    protected function _parseActionPart()
    {
        // the next "word" should be the action name
        $cliActionName = array_shift($this->_argumentsWorking);

        if ($cliActionName == '?') { // || strtolower($cliActionName) == 'help') {
            $this->_help = true;
            return;
        }
        
        // is the action name valid?
        $cliActionMetadata = $this->_manifestRepository->getMetadata(
            array('type' => 'Action', 'name' => 'cliActionName', 'value' => $cliActionName)
            );

        // if no action, handle error
        if (!$cliActionMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Action \'' . $cliActionName . '\' is not a valid action.');
        }

        // prepare action request name
        $this->_request->setActionName($cliActionMetadata->getActionName());
        $this->_metadataAction = $cliActionMetadata;
        return;
    }

    /**
     * Internal routine for parsing the provider part of the command line arguments
     *
     * @return null
     */
    protected function _parseProviderPart()
    {
        // get the cli "word" as the provider name from command line
        $cliProviderFull = array_shift($this->_argumentsWorking);
        $cliSpecialtyName = '_global';

        // if there is notation for specialties? If so, break them up
        if (strstr($cliProviderFull, '.')) {
            list($cliProviderName, $cliSpecialtyName) = explode('.', $cliProviderFull);
        } else {
            $cliProviderName = $cliProviderFull;
        }

        if ($cliProviderName == '?' || $cliSpecialtyName == '?') {
            $this->_help = true;
            $this->_request->setProviderName($cliProviderName);
            $this->_request->setSpecialtyName($cliSpecialtyName);
            return;
        }
        
        // get the cli provider names from the manifest
        $cliProviderMetadata = $this->_manifestRepository->getMetadata(
            array('type'=>'Provider', 'name' => 'cliProviderName', 'value' => $cliProviderName)
            );

        if (!$cliProviderMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception(
                'Provider \'' . $cliProviderFull . '\' is not a valid provider.'
                );
        }

        $cliSpecialtyMetadata = $this->_manifestRepository->getMetadata(array(
            'type' => 'Provider', 
            'name' => 'cliSpecialtyNames', 
            'providerName' => $cliProviderMetadata->getProviderName(), 
            'value' => $cliSpecialtyName)
            );

        if (!$cliSpecialtyMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception(
                'Provider \'' . $cliSpecialtyName . '\' is not a valid specialty.'
                );
        }

        // prepare provider request name
        $this->_request->setProviderName($cliProviderMetadata->getProviderName());
        $this->_metadataProvider = $cliProviderMetadata;
        $this->_request->setSpecialtyName($cliSpecialtyMetadata->getSpecialtyName());
        $this->_metadataSpecialty = $cliSpecialtyMetadata;
        return;
    }

    /**
     * Internal routine for parsing the provider options from the command line
     *
     * @return null
     */
    protected function _parseProviderOptionsPart()
    {
        $searchParams = array(
            'type'          => 'Provider',
            'providerName'  => $this->_request->getProviderName(),
            'actionName'    => $this->_request->getActionName(),
            'specialtyName' => $this->_request->getSpecialtyName(),
            );

        $cliActionableMethodLongParameterMetadata = $this->_manifestRepository->getMetadata(
            array_merge($searchParams, array('name' => 'cliActionableMethodLongParameters'))
            );

        $cliActionableMethodShortParameterMetadata = $this->_manifestRepository->getMetadata(
            array_merge($searchParams, array('name' => 'cliActionableMethodShortParameters'))
            );

        $cliParameterNameShortValues = $cliActionableMethodShortParameterMetadata->getValue();

        $getoptOptions = array();
        $wordArguments = array();

        $cliActionableMethodLongParameterMetadataReference = $cliActionableMethodLongParameterMetadata->getReference();
        foreach ($cliActionableMethodLongParameterMetadata->getValue() as $parameterNameLong => $cliParameterNameLong) {
            $optionConfig = $cliParameterNameLong . '|';

            $parameterInfo = $cliActionableMethodLongParameterMetadataReference['parameterInfo'][$parameterNameLong];

            // process ParameterInfo into array for command line option matching
            if ($parameterInfo['type'] == 'string' || $parameterInfo['type'] == 'bool') {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] 
                               . (($parameterInfo['optional']) ? '-' : '=') . 's';
            } elseif (in_array($parameterInfo['type'], array('int', 'integer', 'float'))) {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] 
                               . (($parameterInfo['optional']) ? '-' : '=') . 'i';
            } else {
                $optionConfig .= $cliParameterNameShortValues[$parameterNameLong] . '-s';
            }

            $getoptOptions[$optionConfig] = ($parameterInfo['description'] != '') ? $parameterInfo['description'] : 'No description available.';


            // process ParameterInfo into array for command line WORD (argument) matching
            $wordArguments[$parameterInfo['position']]['parameterName'] = $parameterInfo['name'];
            $wordArguments[$parameterInfo['position']]['optional']      = $parameterInfo['optional'];
            $wordArguments[$parameterInfo['position']]['type']          = $parameterInfo['type'];

        }


        if (!$getoptOptions) {
            // no options to parse here, return
            return;
        }

        // if non-option arguments exist, attempt to process them before processing options
        $wordStack = array();
        while ($wordOnTop = array_shift($this->_argumentsWorking)) {
            if (substr($wordOnTop, 0, 1) != '-') {
                array_push($wordStack, $wordOnTop);
            } else {
                // put word back on stack and move on
                array_unshift($this->_argumentsWorking, $wordOnTop);
                break;
            }

            if (count($wordStack) == count($wordArguments)) {
                // when we get at most the number of arguments we are expecting
                // then break out.
                break;
            }

        }

        if ($wordStack && $wordArguments) {
            for ($wordIndex = 1; $wordIndex <= count($wordArguments); $wordIndex++) {
                if (!array_key_exists($wordIndex-1, $wordStack) || !array_key_exists($wordIndex, $wordArguments)) {
                    break;
                }
                $this->_request->setProviderParameter($wordArguments[$wordIndex]['parameterName'], $wordStack[$wordIndex-1]);
                unset($wordStack[$wordIndex-1]);
            }
        }

        $getoptParser = new Zend_Console_Getopt($getoptOptions, $this->_argumentsWorking, array('parseAll' => false));
        $getoptParser->parse();
        foreach ($getoptParser->getOptions() as $option) {
            $value = $getoptParser->getOption($option);
            $this->_providerOptions[$option] = $value;
            $this->_request->setProviderParameter($option, $value);
        }

        $this->_metadataProviderOptionsLong = $cliActionableMethodLongParameterMetadata;
        $this->_metadataProviderOptionsShort = $cliActionableMethodShortParameterMetadata;

        return;
    }

    /**
     * Internal routine for creating help messages
     *
     * @param array $options
     */
    protected function _createHelpResponse(Array $options = array())
    {
        $response = '';

        if (isset($options['error'])) {
            $response .= $options['error'] . PHP_EOL . PHP_EOL;
        }

        $response .= 'Usage: zf <global options> <action name> <action options> <provider name> <provider options>'
                  . PHP_EOL;

        $actionName    = $this->_request->getActionName();
        $providerName  = $this->_request->getProviderName();
        //$specialtyName = $this->_request->getSpecialtyName();
                  
        // both action and provider are known
        if ($actionName != '' && $actionName != '?' && $providerName != '' && $providerName != '?') {
            
            if ($this->_metadataProviderOptionsLong) {
                $response .= '    Options for this action/resource: ' . PHP_EOL;
                $optionsReference = $this->_metadataProviderOptionsLong->getReference();
                $shortNameRef = $this->_metadataProviderOptionsShort->getValue();

                foreach ($this->_metadataProviderOptionsLong->getValue() as $optionName => $cliOptionName) {
                    $response .= '       --' . $cliOptionName . '|-' . $shortNameRef[$optionName];
                    if ($desc = $optionsReference['parameterInfo'][$optionName]['description']) {
                        $response .= ' (\033[32m' . $desc . '\033[37m)' . PHP_EOL;
                    }
                }
            } else {
                $response .= '    There are no options available for this action-provider.';
            }

        // action name is known, provider is not
        } elseif ($actionName != '' && $actionName != '?' && ($providerName == '' || $providerName == '?') ) {

            // find all providers this action applies to
            $providersSupported = array();
            $providerMetadatas = $this->_manifestRepository->getMetadatas(array(
                'type' => 'Provider', 
                'name' => 'cliProviderName'
                ));

            foreach ($providerMetadatas as $providerMetadata) {
                foreach ($providerMetadata->getReference()->getActionableMethods() as $actionableMethod) {
                    if ($actionableMethod['actionName'] == $this->_metadataAction->getActionName()) {
                        $providersSupported[] = $providerMetadata->getValue();
                    }
                }
            }

            $providersSupported = array_unique($providersSupported);

            if ($providersSupported) {
                $response .= '    Supported providers: ' . PHP_EOL . '    ';
                $response .= implode(PHP_EOL . '    ', $providersSupported);
            } else {
                $response .= '    No supported providers for action ' . $this->_metadataAction->getActionName();
            }

        // provider name is known, action is not
        } elseif ($providerName != '' && $providerName != '?' && ($actionName == '' || $actionName == '?')) {
            
            //$actionsSupported = array();
            
            $validActions = $this->_metadataProvider->getReference()->getActions();
            
            $response .= '    Supported actions:' . PHP_EOL;
            
            foreach ($validActions as $validAction) {
                $actionMetadata = $this->_manifestRepository->getMetadata(array(
                    'type' => 'Action',
                    'name' => 'cliActionName',
                    'actionName' => $validAction->getName()
                    ));
                
                $response .= '    ' . $actionMetadata->getValue() . PHP_EOL;
                
            }
            
        } else {

            $providerMetadatas = $this->_manifestRepository->getMetadatas(
               array('type'=>'Provider', 'name' => 'cliProviderName')
               );

            foreach ($providerMetadatas as $providerMetadata) {
                $responseProviderActions = array();
                foreach ($providerMetadata->getReference()->getActionableMethods() as $actionableMethod) {
                    $actionName = $actionableMethod['action']->getName();
                    $actionMetadata = $this->_manifestRepository->getMetadata(
                        array('type'=>'Action', 'name'=>'cliActionName', 'actionName'=>$actionName)
                        );
                    $responseProviderActions[] = $actionMetadata->getValue();
                }
                $responseProviderActions = array_unique($responseProviderActions);
                $response .= "    (" . implode('|', $responseProviderActions) . ") " . $providerMetadata->getValue() . PHP_EOL;
            }

        }

        $this->_response->appendContent($response);
        return;
    }

}
