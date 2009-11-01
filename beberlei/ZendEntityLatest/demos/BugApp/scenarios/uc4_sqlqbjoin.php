<?php

require_once dirname(__FILE__)."/../bootstrap.php";

$sqlQueryBuilder = new Zend_Db_Mapper_SqlQueryBuilder($entityManager);
$sqlQueryBuilder->fromEntity("Bug", "b")
                ->joinEntity("User", "e.account_id = b.assigned_to", "e")
                ->joinEntity("User", "r.account_id = b.reported_by", "r")
                ->order("b.bug_created DESC")
                ->limit(20);

$bugs = $sqlQueryBuilder->getResultList();

foreach($bugs AS $bug) {

    echo $bug->id.": ".$bug->description." - ".$bug->created->format('d.m.Y')."\n";
    echo "    Reported by: ".$bug->reporter->name."\n";
    echo "    Assigned to: ".$bug->engineer->name."\n";
    foreach($bug->products AS $product) {
        echo "    Platform: ".$product->name."\n";
    }
    echo "\n";
}