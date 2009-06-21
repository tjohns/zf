<?php
/*
 * Scenario 1: Find and display a specific Invoice
 */

// $invoiceId would be set from a request parameter
$invoice = $entityManager->load('Invoice', $invoiceId);

// Display the Invoice (not using HTML, for clarity)
echo 'Customer: ',     $invoice->getCustomer()->getName(), "\n",
     'Invoice #',      $invoice->getId(), "\n",
     'Invoice Date: ', $invoice->getDate(), "\n",
     'Description: ',  $invoice->getDescription(), "\n";

echo "Bill to: \n", $invoice->getCustomer()->getBillingAddr(), "\n\n",
     "Ship to: \n", $invoice->getCustomer()->getPhysicalAddr(), "\n\n";

echo "Line # \t Description \t Item \t Taxable \t Amount \n";
foreach ($invoice->getItems() as $invItem) {
    echo $invItem->getLineNo(), "\t",
         $invItem->getDesscription(), "\t",
         $invItem->getItem()->getName(), "\t",
         $invItem->getTaxable()? 'Yes': 'No', "\t",
         $invItem->getAmount(), "\n";
}

echo 'Invoice Total: ', $invoice->calculateTotal(), "\n";
