<?php

class Zend_Doctrine2_QueryLog implements \Doctrine\DBAL\Logging\SqlLogger
{
    /**
     * @var Zend_Log
     */
    protected $_log = null;

    /**
     * @var string
     */
    protected $_priority = null;

    /**
     * @param array|Zend_Config $options
     */
    public function __construct($options=array())
    {
        if(is_array($options) || $options instanceof Zend_Config) {
            foreach($options AS $k => $v) {
                $method = "set".$k;
                if(method_exists($this, $method)) {
                    $this->$method($v);
                }
            }
        }
    }

    /**
     * @param Zend_Log $logger
     * @return Zend_Doctrine2_QueryLog
     */
    public function setLogger(Zend_Log $logger)
    {
        $this->_log = $logger;
        return $this;
    }

    /**
     * @param string $priority
     * @return Zend_Doctrine2_QueryLog
     */
    public function setPriority($priority)
    {
        $this->_priority = $priority;
        return $this;
    }

    /**
     * @param string $sql
     * @param array $params
     */
    public function logSql($sql, array $params = null)
    {
        $message = $sql." (".implode(", ", array_map(array($this, "escapeParams"), $params));
        $this->_log->log($message, $this->_priority);
    }

    /**
     * @param  string $val
     * @return string
     */
    protected function escapeParams($val)
    {
        return "'".$val."'";
    }
}