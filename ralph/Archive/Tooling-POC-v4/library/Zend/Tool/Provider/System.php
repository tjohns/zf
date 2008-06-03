<?php

class Zend_Tool_Provider_System extends Zend_Tool_Provider_Abstract
{

    protected $_specialties = array('Providers', 'Help');
    
    public function listProviders()
    {
        $output = '';
        
        foreach ($this->_manifest->getProviders() as $provider) {
            $output .= $provider->getName() . ' [';
            if (is_array($actions = $provider->getActions())) {
                $output .= implode(', ', $actions);
            }
            $output .= ']' . PHP_EOL;
        }
        
        $this->_response->setContent($output);
    }
    
    public function showHelp()
    {
        $output = <<<EOS
Available commands:
    zf list system.providers
        This command will list out all the providers that are in the system.
        
        
EOS;
        $this->_response->setContent($output);
    }
    
    /**
     * TestAction()
     *
     * @param string $test shortName=w longName=words This option allows for test to be switched.
     * @return bool
     */
    public function testAction($words)
    {
        Zend_Debug::dump($words);
        
    }
    
}
