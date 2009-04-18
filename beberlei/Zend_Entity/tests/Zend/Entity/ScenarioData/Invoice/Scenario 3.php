<?php
/*
 * Scenario 3: Make payment of an Invoice
 */

// $form contains user input from a form submission, or similar

$entityManager->beginTransaction();

$customer = $entityManager->findByKey('Customer', $form['customer_id']);
$invoice  = $entityManager->findByKey('Invoice',  $form['invoice_id']);

$account = $customer->getAccount();
$account->payInvoice($invoice, $form['payment_amount']);

$entityManager->save($account);

$entityManager->commit();
