<?php

require_once dirname(__FILE__)."/../bootstrap.php";

$sqlQueryBuilder = new Zend_Db_Mapper_SqlQueryBuilder($entityManager);
$sqlQueryBuilder->fromEntity("Bug")
                ->order("bug_created DESC");

$bugs = $sqlQueryBuilder->getResultList();

foreach($bugs AS $bug) {
    echo $bug->description." - ".$bug->created->format('d.m.Y')."\n";
}

xdebug_var_dump($bugs);