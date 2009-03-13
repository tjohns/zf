<?php
/**
 * 
 */

require_once 'Zend/Loader.php';
require_once 'Zend/Tool/Framework/Client/Abstract.php';
require_once 'Zend/Tool/Framework/Client/Cli/ArgumentParser.php';
require_once 'Zend/Tool/Framework/Client/Interactive/InputInterface.php';
require_once 'Zend/Tool/Framework/Client/Interactive/OutputInterface.php';


/**
 * Zend_Tool_Framework_Client_Cli - the CLI Client implementation for Zend_Tool_Framework
 *
 */
class Zend_Tool_Framework_CliClient 
    extends Zend_Tool_Framework_Client_Abstract
    implements Zend_Tool_Framework_Client_Interactive_InputInterface,
               Zend_Tool_Framework_Client_Interactive_OutputInterface 
{

    /**
     * @var Zend_Filter_Word_CamelCaseToDash
     */
    protected $_filterToClientNaming = null;
    
    /**
     * @var Zend_Filter_Word_DashToCamelCase
     */
    protected $_filterFromClientNaming = null;
    
    /**
     * main() - This is typically called from zf.php. This method is a 
     * self contained main() function.
     *
     */
    public static function main()
    {
        ini_set('display_errors', true);
        $cliClient = new self();
        $cliClient->dispatch();
    }

    /**
     * _init() - Tasks processed before the constructor
     *
     */
    protected function _init()
    {
        // support the changing of the current working directory, necessary for some providers
        if (isset($_ENV['PWD'])) {
            chdir($_ENV['PWD']);
        }
        
        // support setting the loader from the environment
        if (isset($_ENV['ZEND_TOOL_FRAMEWORK_LOADER_CLASS']) && Zend_Loader::loadClass($_ENV['ZEND_TOOL_FRAMEWORK_LOADER_CLASS'])) {
            $this->_clientRegistry->setLoader(new $_ENV['ZEND_TOOL_FRAMEWORK_LOADER_CLASS']);
        }
    }

    /**
     * _preDispatch() - Tasks handed after construction but before dispatching
     *
     */
    protected function _preDispatch()
    {
        $optParser = new Zend_Tool_Framework_Client_Cli_ArgumentParser($_SERVER['argv']);
        $optParser->parse();
    }

    /**
     * _postDispatch() - Tasks handled after dispatching
     *
     */
    protected function _postDispatch()
    {
        if ($this->_clientRegistry->getResponse()->isException()) {
            echo PHP_EOL 
               . 'An error has occured:' 
               . PHP_EOL
               . $this->_clientRegistry->getResponse()->getException()->getMessage() 
               . PHP_EOL;
        }
    }

    /**
     * handleInteractiveInputRequest() is required by the Interactive InputInterface
     * 
     *
     * @param Zend_Tool_Framework_Client_Interactive_InputRequest $inputRequest
     * @return string 
     */
    public function handleInteractiveInputRequest(Zend_Tool_Framework_Client_Interactive_InputRequest $inputRequest)
    {
        fwrite(STDOUT, $inputRequest->getContent() . PHP_EOL . 'zf> ');
        $inputContent = fgets(STDIN);
        return substr($inputContent, 0, -1); // remove the return from the end of the string
    }
    
    /**
     * handleInteractiveOutput() is required by the Interactive OutputInterface
     * 
     * This allows us to display output immediately from providers, rather
     * than displaying it after the provider is done.
     *
     * @param string $output
     */
    public function handleInteractiveOutput($output)
    {
        echo $output . PHP_EOL;
    }
    


    
    /**
     * convertToClientNaming()
     * 
     * Convert words to client specific naming, in this case is lower, dash separated
     *
     * Filters are lazy-loaded.
     * 
     * @param string $string
     * @return string
     */
    public function convertToClientNaming($string)
    {
        if (!$this->_filterToClientNaming) {
            require_once 'Zend/Filter.php';
            require_once 'Zend/Filter/Word/CamelCaseToDash.php';
            require_once 'Zend/Filter/StringToLower.php';
            $filter = new Zend_Filter();
            $filter->addFilter(new Zend_Filter_Word_CamelCaseToDash());
            $filter->addFilter(new Zend_Filter_StringToLower());
            
            $this->_filterToClientNaming = $filter;
        }
        
        return $this->_filterToClientNaming->filter($string);
    }
    
    /**
     * convertFromClientNaming()
     *
     * Convert words from client specific naming to code naming - camelcased
     * 
     * Filters are lazy-loaded.
     * 
     * @param string $string
     * @return string
     */
    public function convertFromClientNaming($string)
    {
        if (!$this->_filterFromClientNaming) {
            require_once 'Zend/Filter/Word/DashToCamelCase.php';
            $this->_filterFromClientNaming = new Zend_Filter_Word_DashToCamelCase();
        }
        
        return $this->_filterFromClientNaming->filter($string);
    }

}
