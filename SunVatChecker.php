<?php

/**
 * SunVatChecker Class
 *
 * @category  Vat Validation
 * @package   SunVatChecker
 * @author    Mehmet Selcuk Batal <batalms@gmail.com>
 * @copyright Copyright (c) 2022, Sunhill Technology <www.sunhillint.com>
 * @license   https://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/msbatal/PHP-EU-VAT-Number-Validation
 * @version   1.1.3
 */

class SunVatChecker
{

  /**
   * EU Country List
   * @var array
   */
  protected const EU_COUNTRY_LIST = [
    'Austria' => [
      'code' => 'AT',
      'length' => 10,
      'pattern' => '/U\d{9}/'
    ],
    'Belgium' => [
      'code' => 'BE',
      'length' => 10,
      'pattern' => '/\d{10}/'
    ],
    'Bulgaria' => [
      'code' => 'BG',
      'length' => 10,
      'pattern' => '/\d{10}/'
    ],
    'Cyprus' => [
      'code' => 'CY',
      'length' => 9,
      'pattern' => '/\d{8}[A-Z]/'
    ],
    'Czech Republic' => [
      'code' => 'CZ',
      'length' => 10,
      'pattern' => '/\d{10}/'
    ],
    'Germany' => [
      'code' => 'DE',
      'length' => 9,
      'pattern' => '/\d{9}/'
    ],
    'Denmark' => [
      'code' => 'DK',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Estonia' => [
      'code' => 'EE',
      'length' => 9,
      'pattern' => '/\d{9}/'
    ],
    'Greece' => [
      'code' => 'EL',
      'length' => 9,
      'pattern' => '/\d{9}/'
    ],
    'Spain' => [
      'code' => 'ES',
      'length' => 9,
      'pattern' => '/[A-Z]\d{2}(?:\d{6}|\d{5}[A-Z])/'
    ],
    'Finland' => [
      'code' => 'FI',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'France' => [
      'code' => 'FR',
      'length' => 11,
      'pattern' => '/\d{11}/'
    ],
    'Croatia' => [
      'code' => 'HR',
      'length' => 11,
      'pattern' => '/\d{11}/'
    ],
    'Hungary' => [
      'code' => 'HU',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Ireland' => [
      'code' => 'IE',
      'length' => 9,
      'pattern' => '/(\d{7}[A-Z]{1,2}|(\d{1}[A-Z]{1}\d{5}[A-Z]{1}))/'
    ],
    'Italy' => [
      'code' => 'IT',
      'length' => 11,
      'pattern' => '/\d{11}/'
    ],
    'Luxembourg' => [
      'code' => 'LU',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Latvia' => [
      'code' => 'LV',
      'length' => 11,
      'pattern' => '/\d{11}/'
    ],
    'Lithuania' => [
      'code' => 'LT',
      'length' => 12,
      'pattern' => '/\d{12}/'
    ],
    'Malta' => [
      'code' => 'MT',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Netherlands' => [
      'code' => 'NL',
      'length' => 12,
      'pattern' => '/\d{9}B\d{2}/'
    ],
    'Poland' => [
      'code' => 'PL',
      'length' => 10,
      'pattern' => '/\d{10}/'
    ],
    'Portugal' => [
      'code' => 'PT',
      'length' => 9,
      'pattern' => '/\d{9}/'
    ],
    'Romania' => [
      'code' => 'RO',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Sweden' => [
      'code' => 'SE',
      'length' => 12,
      'pattern' => '/\d{12}/'
    ],
    'Slovenia' => [
      'code' => 'SI',
      'length' => 8,
      'pattern' => '/\d{8}/'
    ],
    'Slovakia' => [
      'code' => 'SK',
      'length' => 10,
      'pattern' => '/\d{10}/'
      ]
  ];

  /**
   * Country name
   * @var string
   */
  private $country = null;

  /**
   * Company details
   * @var array
   */
  private $details = [];

  /**
   * Error details
   * @var string
   */
  private $error = null;

  /**
   * VIES SOAP URL
   * @var string
   */
  protected $soapUrl = 'https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

  /**
   * VIES API URL
   * @var string
   */
  protected $apiUrl = 'https://ec.europa.eu/taxation_customs/vies/rest-api/ms/$1/vat/$2';

  public function __construct() {
    set_exception_handler(function ($exception) {
      echo '<b>[SunClass] Exception:</b> ' . $exception->getMessage();
    });
  }

  /**
   * Remove country code from VAT ID (if consists)
   *
   * @param string $vatId
   * @return string
   */
  private function splitVatId($vatId = '') {
    $vatId = $this->filterArgument($vatId);
    if (ctype_alpha(substr($vatId, 0, 2))) {
      $vatNumber = substr($vatId, 2);
    } else {
      $vatNumber = $vatId;
    }
    $result = substr($vatNumber, 0, self::EU_COUNTRY_LIST[$this->country]['length']);
    return $result;
  }

  /**
   * Filter data before sending to the VIES service
   *
   * @param string $argument
   * @return string
   */
  private function filterArgument($argument = '') {
    $argument = str_replace(['.', ',', '-', ' '], '', $argument);
    $result = filter_var($argument, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    return $result;
  }

  /**
   * Validate data to prevent XSS and nasty things
   *
   * @param string $argument
   * @return bool
   */
  private function validateArgument($argument = '') {
    $regexp = '/^[a-zA-Z0-9\s\.\-,&\+\(\)\/º\pL]+$/u';
    if (filter_var($argument, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regexp]]) === false) {
      return false;
    }
    return true;
  }

  /**
   * Validate country is intra-EU or not
   * 
   * @param string $country
   * @return bool
   */
  private function validateCountry($country = '') {
    $country = ucwords(strtolower($country));
    if (!isset(self::EU_COUNTRY_LIST[$country])) {
      return false;
    }
    return true;
  }

  /**
   * Validate EU VAT ID
   * 
   * @param string $country
   * @param string $vatId
   * @return bool
   */
  public function validate($country = '', $vatId = '') {
    if (empty($country) || empty($vatId)) {
      $this->error = 'Country or VAT ID can not be empty';
      return false;
    }
    if ($this->validateCountry($country) === false) {
      $this->error = $country . ' is not an EU country';
      return false;
    }
    $this->country = ucwords(strtolower($country));
    $countryCode = self::EU_COUNTRY_LIST[$this->country]['code'];
    $vatNumber = $this->splitVatId(strtoupper($vatId));
    $vatId = $countryCode.$vatNumber;
    if (preg_match(self::EU_COUNTRY_LIST[$this->country]['pattern'], $vatNumber)) {
      if ($this->validateArgument($vatId)) {
        if (extension_loaded('soap')) {
          $query = new SoapClient($this->soapUrl);
          $result = $query->checkVat(array('countryCode' => $countryCode, 'vatNumber' => $vatNumber));
          $resArr = json_decode(json_encode($result), true);
          $this->details = $resArr;
          if ($resArr['valid'] == true) {
            return true;
          } else {
            $this->error = 'SOAP Server: ' . $vatId . ' is not a valid EU VAT Number';
            return false;
          }
        } else {
          $query = str_replace(['$1', '$2'], [$countryCode, $vatNumber], $this->apiUrl);
          $result = file_get_contents($query);
          $resArr = json_decode($result, true);
          $this->details = $resArr;
          if ($resArr["isValid"] == true) {
            return true;
          } else {
            $this->error = 'API Server: ' . $vatId . ' is not a valid EU VAT Number';
            return false;
          }
        }
      } else {
        $this->error = $vatId . ' VAT ID includes malicious characters';
        return false;
      }
    } else {
      $this->error = $vatNumber . ' VAT Number is not in correct format';
      return false;
    }
  }

  /**
   * Return company details
   * 
   * @param string $country
   * @param string $vatId
   * @return array
   */
  public function getDetails($country = '', $vatId = '') {
    if ($this->validate($country, $vatId) === true) {
      return $this->details;
    } else {
      return ['Company details could not be found'];
    }
  }

  /**
   * Return last error
   *
   * @return string
   */
  public function getError() {
    return $this->error;
  }

}

?>