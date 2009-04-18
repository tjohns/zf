<?php
/*
 * Scenario 5: Creating or updating an Address
 */

// $form contains user input form a form submission, or similar. If the user is 
// updating an existing Address, there will be a $form['address_id'] with the 
// Primary Key of the existing address. Otherwise, we assume they are creating a 
// new Address.

if (!empty($form['address_id'])) {
    $address = $entityManager->findByKey('Address', $form['address_id']);
} else {
    $address = new Address;
}

$address->setStreetName( $form['street_name'] );
$address->setUnitNo(     $form['unit_no']     );
$address->setAddrLine2(  $form['addr_line2']  );
$address->setCity(       $form['city']        );
$address->setState(      $form['state']       );
$address->setZipcode(    $form['zipcode']     );

$entityManager->save($address);
