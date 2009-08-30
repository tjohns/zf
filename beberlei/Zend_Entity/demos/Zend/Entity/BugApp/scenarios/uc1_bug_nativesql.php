<?php

require_once dirname(__FILE__)."/../bootstrap.php";

$rsm = new Zend_Entity_Query_ResultSetMapping();
$rsm->addEntity("Bug", "b")
    ->addProperty("b", "bug_id", "id")
    ->addProperty("b", "bug_description", "description")
    ->addProperty("b", "bug_created", "created")
    ->addProperty("b", "bug_status", "status")
    ->addProperty("b", "reported_by", "reporter")
    ->addProperty("b", "assigned_to", "engineer");

/**
CREATE PROCEDURE GetBugs()
BEGIN
    SELECT * FROM zfbugs;
END
 */
$sql = "CALL GetBugs();";

$query = $entityManager->createNativeQuery($sql, $rsm);
$bugs = $query->getResultList();

foreach($bugs AS $bug) {
    echo $bug->description." - ".$bug->created->format('d.m.Y')."\n";
    echo "    Reported by: ".$bug->reporter->name."\n";
    echo "    Assigned to: ".$bug->engineer->name."\n";
    foreach($bug->products AS $product) {
        echo "    Platform: ".$product->name."\n";
    }
    echo "\n";
}

#xdebug_var_dump($bugs);