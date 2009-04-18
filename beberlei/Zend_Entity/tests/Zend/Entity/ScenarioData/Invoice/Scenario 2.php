<?php
/*
 * Scenario 2: List a Customer's Invoices
 */

// $customerId would be set from a request parameter
$select = $entityManager->select('Invoice');
$select->where('customer_id = ?', $customerId);

$customerInvoices = $entityManager->find('Invoice', $select);

foreach ($customerInvoices as $invoice) {
    echo 'Invoice #',      $invoice->getId(), "\t",
         'Invoice Date: ', $invoice->getDate(), "\t",
         'Customer: ',     $invoice->getCustomer()->getName(), "\n";
}
