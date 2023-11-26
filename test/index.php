<?php

  require_once ('SunVatChecker.php'); // Call 'SunVatChecker' class

  $vatChecker = new SunVatChecker(); // Create object

  $country = "Estonia"; // EU country (or any country)
  $vatId = "123456789"; // VAT Number (or VAT ID, EE123456789)

  // Validate VAT Number
  if ($vatChecker->validate($country, $vatId) === true) {
    echo "VAT Number is valid!";
  } else {
    echo "VAT Number is invalid!";
  }

  // Get company details
  $details = $vatChecker->getDetails($country, $vatId);
  //var_dump($details);
  foreach ($details as $key => $value) {
    echo $key . ' = ' . $value . '<br>';
  }

  // Get company details
  echo 'VAT Number: ' . $details['vatNumber'] . '<br>';
  echo 'Company Name: ' . $details['name'] . '<br>';
  echo 'Company Address: ' . $details['address'];

  // Print last error
  echo $vatChecker->getError();

?>