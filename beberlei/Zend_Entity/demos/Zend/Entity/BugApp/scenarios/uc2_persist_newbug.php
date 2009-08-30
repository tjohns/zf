<?php
require_once dirname(__FILE__)."/../bootstrap.php";

$theProductId = 1;
$theReporterId = 1;
$theDefaultEngineerId = 2;

$product = $entityManager->load("Product", $theProductId);
$reporter = $entityManager->load("User", $theReporterId);
$engineer = $entityManager->load("User", $theDefaultEngineerId);

$bug = new Bug();
$bug->description = "Something does not work!";
$bug->assignToProduct($product);
$bug->setEngineer($engineer);
$bug->setReporter($reporter);
$bug->created = new DateTime("now");
$bug->status = "NEW";

$entityManager->save($bug);
echo "My Bug Id: ".$bug->id."\n";