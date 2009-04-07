<?php

class Zend_Tool_Project_Provider_DbAdapter extends Zend_Tool_Project_Provider_Abstract
{
    
    
    public function configure($adapter = null, $parameters = null, $keyForConfig = 'database', $sectionForConfig = 'production')
    {
        $profile = $this->_loadProfile();
        
        $client = Zend_Tool_Framework_Registry::getInstance()->getClient();
        
        if (!$adapter && !$parameters && $client->hasInteractiveInput()) {
            
            $adapterResponse  = $client->promptInteractiveInput('Please enter the database adapter (currently only "PDO_MYSQL")');
            $usernameResponse = $client->promptInteractiveInput('Please enter the database username');
            $passwordResponse = $client->promptInteractiveInput('Please enter the database password');
            $dbnameResponse   = $client->promptInteractiveInput('Please enter the database dbname');
            
            echo 'setting up'
               . 'driver: ' . $adapterResponse->getContent() . PHP_EOL
               . 'username: ' . $usernameResponse->getContent() . PHP_EOL
               . 'password: ' . $passwordResponse->getContent() . PHP_EOL
               . 'dbname: ' . $dbnameResponse->getContent() . PHP_EOL
               ;
            
        } elseif ($adapter && $parameters) { 
        
            parse_str($parameters, $parameters);
        
        } else {
            throw new Exception('No driver or parameters was supplied and interactivity was not supported');
        }
        
        // get the config file
        $applicationConfigFileResource = $profile->search('applicationConfigFile');
        
        $configPath = $applicationConfigFileResource->getPath();
        
        $configDbValues[$keyForConfig]['adapter'] = strtoupper($adapter);
        foreach ($parameters as $paramName => $paramValue) {
            $configDbValues[$keyForConfig]['params'][$paramName] = $paramValue;
        }
        
        $this->_registry->getResponse()->appendContent('Writing configuration values to config file ' . $configPath);
        $this->_writeValuesToConfigFile($configPath, $sectionForConfig, $configDbValues);
        
        $bootstrapFileResource = $profile->search('bootstrapFile');
        $bootstrapFilePath = $bootstrapFileResource->getPath();
        
        $this->_registry->getResponse()->appendContent('Adding $dbAdapter to the bootstrap file at ' . $bootstrapFilePath);
        $this->_writeDbAdapterToBootstrapFile($bootstrapFilePath);
        
    }
    
    public function testConnection()
    {
        $profile = $this->_loadProfile();
        $bootstrapFileResource = $profile->search('bootstrapFile');
        
        $dbAdapter = $this->_loadBootstrapReturnVar($bootstrapFileResource->getPath(), 'dbAdapter');
        $this->_registry->getResponse()->appendContent('Sending query "SELECT 1=1 as test"');
        $result = $dbAdapter->fetchAll('SELECT 1=1 as test');
        $this->_registry->getResponse()->appendContent('Got result: ' . $result[0]['test']);
        $this->_registry->getResponse()->appendContent('Db Connection appears to be working.');
        
    }
    
    protected function _writeValuesToConfigFile($configPath, $configSection, $configValuesToMerge)
    {
        // get config file
        require_once 'Zend/Config/Ini.php';
        $config = new Zend_Config_Ini($configPath, null, array('skipExtends' => true,'allowModifications' => true));
        
        // merge new contents
        $config->{$configSection}->merge(new Zend_Config($configValuesToMerge));
        
        // get writer
        require_once 'Zend/Config/Writer/Ini.php';
        $writer = new Zend_Config_Writer_Ini(array('config' => $config, 'filename' => $configPath));
        $writer->write();
    }
    
    protected function _writeDbAdapterToBootstrapFile($bootstrapPath)
    {
        $bootstrapContents = file_get_contents($bootstrapPath);
        $bootstrapContents .= PHP_EOL . PHP_EOL . '$dbAdapter = Zend_Db::factory($config->database);' . PHP_EOL . PHP_EOL;
        file_put_contents($bootstrapPath, $bootstrapContents);
    }
    
    protected function _loadBootstrapReturnVar($___bootstrapPath, $___varToReturn)
    {
        $bootstrap = true;
        include $___bootstrapPath;
        return $$___varToReturn;
    }
    
}
