<?php

require_once 'Zend/Console/Getopt.php';

/**
 * The main
 *
 */
class Zend_Tool_Framework_Client_Console_ArgumentParser implements Zend_Tool_Framework_Registry_EnabledInterface 
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
     * setArguments
     *
     * @param array $arguments
     * @return Zend_Tool_Framework_Client_Console_ArgumentParser
     */
    public function setArguments(Array $arguments)
    {
        $this->_argumentsOriginal = $this->_argumentsWorking = $arguments;
        return $this;
    }
    
    /**
     * setRegistry()
     *
     * @param Zend_Tool_Framework_Registry_Interface $registry
     * @return Zend_Tool_Framework_Client_Console_ArgumentParser
     */
    public function setRegistry(Zend_Tool_Framework_Registry_Interface $registry)
    {
        // get the client registry
        $this->_registry = $registry;
        
        // set manifest repository, request, response for easy access
        $this->_manifestRepository = $this->_registry->getManifestRepository();
        $this->_request  = $this->_registry->getRequest();
        $this->_response = $this->_registry->getResponse();
        return $this;
    }
    
    /**
     * Parse() - This method does the work of parsing the arguments into the enpooint request,
     * this will also (during help operations) fill the response in with information as needed
     *
     * @return null
     */
    public function parse()
    {

        if ($this->_request == null || $this->_response == null) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('The client registry must have both a request and response registered.');
        }
        
        // check to see if the first cli arg is the script name
        if ($this->_argumentsWorking[0] == $_SERVER['SCRIPT_NAME' ]) {
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
            } elseif ($option == 'debug') {
                $this->_request->setDebug(true);
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
        $consoleActionName = array_shift($this->_argumentsWorking);

        if ($consoleActionName == '?') { // || strtolower($consoleActionName) == 'help') {
            $this->_help = true;
            return;
        }

        // is the action name valid?
        $actionMetadata = $this->_manifestRepository->getMetadata(array(
            'type'       => 'Tool',
            'name'       => 'actionName',
            'value'      => $consoleActionName,
            'clientName' => 'console'
            ));

        // if no action, handle error
        if (!$actionMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception('Action \'' . $consoleActionName . '\' is not a valid action.');
        }

        // prepare action request name
        $this->_request->setActionName($actionMetadata->getActionName());
        $this->_metadataAction = $actionMetadata;
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
        $consoleProviderFull = array_shift($this->_argumentsWorking);
        $consoleSpecialtyName = '_global';

        // if there is notation for specialties? If so, break them up
        if (strstr($consoleProviderFull, '.')) {
            list($consoleProviderName, $consoleSpecialtyName) = explode('.', $consoleProviderFull);
        } else {
            $consoleProviderName = $consoleProviderFull;
        }

        if ($consoleProviderName == '?' || $consoleSpecialtyName == '?') {
            $this->_help = true;
            $this->_request->setProviderName($consoleProviderName);
            $this->_request->setSpecialtyName($consoleSpecialtyName);
            return;
        }
        
        // get the cli provider names from the manifest
        $providerMetadata = $this->_manifestRepository->getMetadata(array(
            'type'       => 'Tool',
            'name'       => 'providerName',
            'value'      => $consoleProviderName,
            'clientName' => 'console'
            ));

        if (!$providerMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception(
                'Provider \'' . $consoleProviderFull . '\' is not a valid provider.'
                );
        }

        $providerSpecialtyMetadata = $this->_manifestRepository->getMetadata(array(
            'type'         => 'Tool', 
            'name'         => 'providerSpecialtyNames', 
            'value'        => $consoleSpecialtyName,
            'providerName' => $providerMetadata->getProviderName(),
            'clientName'   => 'console'
            ));

        if (!$providerSpecialtyMetadata) {
            require_once 'Zend/Tool/Framework/Client/Exception.php';
            throw new Zend_Tool_Framework_Client_Exception(
                'Provider \'' . $consoleSpecialtyName . '\' is not a valid specialty.'
                );
        }

        // prepare provider request name
        $this->_request->setProviderName($providerMetadata->getProviderName());
        $this->_metadataProvider = $providerMetadata;
        $this->_request->setSpecialtyName($providerSpecialtyMetadata->getSpecialtyName());
        $this->_metadataSpecialty = $providerSpecialtyMetadata;
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
            'type'          => 'Tool',
            'providerName'  => $this->_request->getProviderName(),
            'actionName'    => $this->_request->getActionName(),
            'specialtyName' => $this->_request->getSpecialtyName(),
            'clientName'    => 'console'
            );

        $actionableMethodLongParamsMetadata = $this->_manifestRepository->getMetadata(
            array_merge($searchParams, array('name' => 'actionableMethodLongParams'))
            );

        $actionableMethodShortParamsMetadata = $this->_manifestRepository->getMetadata(
            array_merge($searchParams, array('name' => 'actionableMethodShortParams'))
            );

        $paramNameShortValues = $actionableMethodShortParamsMetadata->getValue();

        $getoptOptions = array();
        $wordArguments = array();

        $actionableMethodLongParamsMetadataReference = $actionableMethodLongParamsMetadata->getReference();
        foreach ($actionableMethodLongParamsMetadata->getValue() as $parameterNameLong => $consoleParameterNameLong) {
            $optionConfig = $consoleParameterNameLong . '|';

            $parameterInfo = $actionableMethodLongParamsMetadataReference['parameterInfo'][$parameterNameLong];

            // process ParameterInfo into array for command line option matching
            if ($parameterInfo['type'] == 'string' || $parameterInfo['type'] == 'bool') {
                $optionConfig .= $paramNameShortValues[$parameterNameLong] 
                               . (($parameterInfo['optional']) ? '-' : '=') . 's';
            } elseif (in_array($parameterInfo['type'], array('int', 'integer', 'float'))) {
                $optionConfig .= $paramNameShortValues[$parameterNameLong] 
                               . (($parameterInfo['optional']) ? '-' : '=') . 'i';
            } else {
                $optionConfig .= $paramNameShortValues[$parameterNameLong] . '-s';
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

        $this->_metadataProviderOptionsLong = $actionableMethodLongParamsMetadata;
        $this->_metadataProviderOptionsShort = $actionableMethodShortParamsMetadata;

        return;
    }

    /**
     * _createHelpResponse
     *
     * @param unknown_type $options
     */
    protected function _createHelpResponse($options = array())
    {
        require_once 'Zend/Tool/Framework/Client/Console/HelpSystem.php';
        $helpSystem = new Zend_Tool_Framework_Client_Console_HelpSystem();
        $helpSystem->setRegistry($this->_registry);
        
        if (isset($options['error'])) {
            $helpSystem->respondWithErrorMessage($options['error']);
        }
        
        $helpSystem->respondWithGeneralHelp();
        
    }

}
