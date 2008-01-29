<?php

require_once 'Zend/Filter.php';
require_once 'Zend/Filter/Word/DashToCamelCase.php';
require_once 'Zend/Filter/Word/UnderscoreToCamelCase.php';

abstract class Zend_Build_Target_Abstract
{
    static protected $_parameterNormalizationFilter = null;
    
    protected $_targetRegistry = null;

    protected $_tasks = array();

    static public function setParameterNormalizationFilter(Zend_Filter_Interface $filter)
    {
        self::$_parameterNormalizationFilter = $filter;
        return;
    }
    
    static public function getParameterNormalizationFilter()
    {
        if (self::$_parameterNormalizationFilter == null) {
            $filter = new Zend_Filter();
            $filter->addFilter(new Zend_Filter_Word_DashToCamelCase())
                   ->addFilter(new Zend_Filter_Word_UnderscoreToCamelCase());
            self::setParameterNormalizationFilter($filter);
        }
        
        return self::$_parameterNormalizationFilter;
    }
    
    public function __construct()
    {
        // setup registry
        $this->_targetRegistry = new Zend_Registry();
        
        // setup filter

    }

    public function getParameterNormalizationFilter()
    {
        return $this->_parameterNormalizationFilter;
    }
    
    public function addTask(Zend_Build_Task $task, $taskIndex = null)
    {
        $this->_tasks[$taskIndex] = $task;
    }

    public function execute()
    {
        foreach ($this->_tasks as $task) {
            $task->setExecutingTarget($this);
            $task->satisfyDependencies(); // catch any issues
        }
        
        try {
            foreach ($this->_tasks as $task) {
                $task->execute();
            }
        } catch (Exception $e) {
            foreach ($this->_tasks as $task) {
                $task->rollback();
            }
        }
        
        foreach ($this->_tasks as $task) {
            $task->cleanup();
            $task->unsetExecutingTarget();
        }
        
    }
    
}