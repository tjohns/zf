<?php
/*
 * Scenario 4: Add or remove InvoiceItems
 */

// 1) Adding a new Invoice Item to an Invoice
// $form contains user input from a form submission

$entityManager->beginTransaction();

$invoice      = $entityManager->load('Invoice', $form['invoice_id']);
$selectedItem = $entityManager->load('Item',    $form['item_id']);

$lastLineNo = 1;
foreach ($invoice->getItems() as $item) {
    $lastLineNo = max($lastLineNo, $item->getLineNo());
}

$invoiceItem = new InvoiceItem;

$invoiceItem->setLineNo(      $lastLineNo + 1      );
$invoiceItem->setTaxable(     $form['taxable']     );
$invoiceItem->setDescription( $form['description'] );
$invoiceItem->setAmount(      $form['amount']      );
$invoiceItem->setItem(        $selectedItem        );

$invoice->getItems()->add($invoiceItem);

$entityManager->save($invoice);
$entityManager->commit();


// 2) Removing an InvoiceItem
// $form contains user input from a form submission

$entityManager->beginTransaction();

$invoice      = $entityManager->load('Invoice', $form['invoice_id']);
$selectedItem = $entityManager->load('Item',    $form['item_id']);

$invoice->getItems()->remove($item);

$entityManager->save($invoice);
$entityManager->commit();
