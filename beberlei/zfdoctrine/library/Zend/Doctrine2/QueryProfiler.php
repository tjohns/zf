<?php

class Zend_Doctrine2_QueryProfiler extends Zend_Db_Profiler
    implements \Doctrine\DBAL\Logging\SqlLogger
{
    /**
     * @param string $sql
     * @param array $params
     */
    public function logSql($sql, array $params = null)
    {
        $queryId = $this->queryStart($sql);
        $this->getQueryProfile($queryId)->bindParams($params);
        $this->queryEnd($queryId);
    }
}