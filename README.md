# PHP EU VAT Number Validation

SunVatChecker is a simple and fast PHP dynamic EU VAT Number Validation class that validates a business EU VAT Number using the EU VIES system.

To use SunVatChecker class, you need to define the `country` and `vat number` parameters you want to verify. If the Country you are sending to is an EU member, the VAT verification step starts, otherwise, a `false` response is sent to the user. If the sent VAT Number is in the correct format, an inquiry is made from the EU VIES system, and a `true` or `false` response is sent to the user depending on the response. If it is in the wrong format or is not a member of the EU, a `false` response is sent to the user.

`Technical Document:` https://www.deepwiki.com/msbatal/PHP-EU-VAT-Number-Validation

<hr>

### Table of Contents

- **[Initialization](#initialization)**
- **[VAT Validation](#vat-validation)**
- **[Company Details](#company-details)**
- **[Error Printing](#error-printing)**

### Installation

Download all files (except Test directory).

To utilize this class, first import SunVatChecker.php into your project, and require it.
SunVatChecker requires PHP 5.5+ to work.

```php
require_once ('SunVatChecker.php');
```

### Initialization

Simple initialization:

```php
$vatChecker = new SunVatChecker();
```

### VAT Validation

Validate VAT Number using Country and VAT ID parameters

```php
$vatChecker = new SunVatChecker();

$country = 'Estonia';
$vatId = '123456789';

if ($vatChecker->validate($country, $vatId) === true) {
    echo "VAT Number is valid!";
} else {
    echo "VAT Number is invalid!";
}
```

VAT ID is valid if the validation result is `true`, invalid if `false`

### Company Details

Get intra-EU company details if has a valid VAT Number

```php
$vatChecker = new SunVatChecker();

$country = 'Estonia';
$vatId = '123456789';

$details = $vatChecker->getDetails($country, $vatId);
```

`details` variable is an array and you can check its content

```php
var_dump($details);
```

or you can parse and use it as key and value

```php
foreach ($details as $key => $value) {
    echo $key . ' = ' . $value . '<br>';
}
```

If you use SOAP, the keys and types are `countryCode` (string), `vatNumber` (string), `requestDate` (datetime), `valid` (boolean), `name` (string), and `address` (string).

```php
echo 'VAT Number: ' . $details['vatNumber'] . '<br>';
echo 'Company Name: ' . $details['name'] . '<br>';
echo 'Company Address: ' . $details['address'];
```

If you use API, the keys and types are `isValid` (boolean), `requestDate` (datetime), `userError` (string), `name` (string), `address` (string), `requestIdentifier` (string), `originalVatNumber` (string), `vatNumber` (string), and `viesApproximate` (array).

```php
echo 'VAT Number: ' . $details['originalVatNumber'] . '<br>';
echo 'Company Name: ' . $details['name'] . '<br>';
echo 'Company Address: ' . $details['address'];
```

### Error Printing

Print the last error if occurred

```php
echo $vatChecker->getError();
```
