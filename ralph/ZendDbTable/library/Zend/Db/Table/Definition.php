<?php

class Zend_Db_Table_Definition
{
    
    protected $_tableConfigs = array();
    
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $this->setConfig($options);
        } elseif (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function setConfig(Zend_Config $config)
    {
        $this->setOptions($config->toArray());
    }
    
    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $this->setTableConfig($optionName, $optionValue);
        }
    }
    
    /**
     * @var string $tableName
     * @var array  $tableConfig
     */
    public function setTableConfig($tableName, $tableConfig)
    {
        
        // @todo logic here
        $tableConfig[Zend_Db_Table::DEFINITION_CONFIG_NAME] = $tableName;
        $tableConfig[Zend_Db_Table::DEFINITION] = $this;
        
        if (!isset($tableConfig[Zend_Db_Table::NAME])) {
            $tableConfig[Zend_Db_Table::NAME] = $tableName;
        }
        
        
         
        $this->_tableConfigs[$tableName] = $tableConfig;
    }
    
    public function getTableConfig($tableName)
    {
        return $this->_tableConfigs[$tableName];
    }
    
    public function removeTableConfig($tableName)
    {
        unset($this->_tableConfigs[$tableName]);
    }
    
    public function hasTableConfig($tableName)
    {
        return (isset($this->_tableConfigs[$tableName]));
    }
    
    /*
    public function __set($tableName, $definition);
    public function __get($tableName);
    public function __unset($tableName);
    public function __isset($tableName);
    */
}
