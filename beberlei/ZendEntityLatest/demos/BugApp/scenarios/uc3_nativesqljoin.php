<?php

require_once dirname(__FILE__)."/../bootstrap.php";

$rsm = new Zend_Entity_Query_ResultSetMapping();
$rsm->addEntity("Bug", "b")
    ->addProperty("b", "bug_id", "id")
    ->addProperty("b", "bug_description", "description")
    ->addProperty("b", "bug_created", "created")
    ->addProperty("b", "bug_status", "status")
    ->addProperty("b", "reporter", "reporter")
    ->addProperty("b", "engineer", "engineer")
    ->addJoinedEntity("User", "r")
    ->addProperty("r", "reporter_id", "id")
    ->addProperty("r", "reporter_name", "name")
    ->addJoinedEntity("User", "e")
    ->addProperty("e", "engineer_id", "id")
    ->addProperty("e", "engineer_name", "name");

$sql = "SELECT b.bug_id, b.bug_description, b.bug_created, ".
       "  b.bug_status, b.reported_by AS reporter, ".
       "  b.assigned_to AS engineer, ".
       "  r.account_id AS reporter_id, r.account_name AS reporter_name, ".
       "  e.account_id AS engineer_id, e.account_name AS engineer_name ".
       "FROM zfbugs b ".
       "INNER JOIN zfaccounts r ON r.account_id = b.reported_by ".
       "INNER JOIN zfaccounts e ON e.account_id = b.assigned_to ".
       "ORDER BY b.bug_created DESC LIMIT 20";

$sqlQuery = $entityManager->createNativeQuery($sql, $rsm);
$bugs = $sqlQuery->getResultList();

foreach($bugs AS $bug) {

    echo $bug->id.": ".$bug->description." - ".$bug->created->format('d.m.Y')."\n";
    echo "    Reported by: ".$bug->reporter->name."\n";
    echo "    Assigned to: ".$bug->engineer->name."\n";
    foreach($bug->products AS $product) {
        echo "    Platform: ".$product->name."\n";
    }
    echo "\n";
}

