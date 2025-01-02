<?php

declare(strict_types=1);

namespace CarltonHonda\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use CarltonHonda\Template\FrontendRenderer;

class VehicleDetailsController
{
  private $request;
  private $response;
  private $renderer;

  public function __construct(
    Request $request,
    Response $response,
    FrontendRenderer $renderer
  ) {
    $this->request = $request;
    $this->response = $response;
    $this->renderer = $renderer;
  }

  private function isValidUkReg(string $reg): bool
  {
    // See [here](https://gist.github.com/danielrbradley/7567269) for UK number plate pattern.
    $pattern = '/(?<Current>^[A-Z]{2}[0-9]{2}[A-Z]{3}$)|(?<Prefix>^[A-Z][0-9]{1,3}[A-Z]{3}$)|(?<Suffix>^[A-Z]{3}[0-9]{1,3}[A-Z]$)|(?<DatelessLongNumberPrefix>^[0-9]{1,4}[A-Z]{1,2}$)|(?<DatelessShortNumberPrefix>^[0-9]{1,3}[A-Z]{1,3}$)|(?<DatelessLongNumberSuffix>^[A-Z]{1,2}[0-9]{1,4}$)|(?<DatelessShortNumberSufix>^[A-Z]{1,3}[0-9]{1,3}$)|(?<DatelessNorthernIreland>^[A-Z]{1,3}[0-9]{1,4}$)|(?<DiplomaticPlate>^[0-9]{3}[DX]{1}[0-9]{3}$)/';
    return (bool) preg_match($pattern, $reg);
  }

  public function dvlaVesCheck(): Response
  {
    $reg = $this->request->query->get('reg');
    // DVLA API requires the registration number to look like: "TE57VRN".
    $reg = strtoupper(str_replace(' ', '', $reg));

    if (!$this->isValidUkReg($reg)) {
      $html = $this->renderer->render('VehicleDetails', ['error' => 'We couldn\'t find that registration number.<br/>Please try again.']);
      $this->response->setContent($html);
      return $this->response;
    }

    $apiUrl = "https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles";
    $apiKey = $_ENV['DVLA_API_KEY'];

    $data = json_encode(['registrationNumber' => $reg]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "x-api-key: $apiKey",
      "Content-Type: application/json"
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($result, true);

    // Check for an error in the DVLA API response.
    if (isset($data['errors'])) {
      $html = $this->renderer->render('VehicleDetails', ['error' => 'We couldn\'t find that registration number.<br/>Please try again.']);
      $this->response->setContent($html);
      return $this->response;
    }

    [$formattedData, $keysNoValueFromApi, $keysNoMapping] = $this->formatData($data);

    $html = $this->renderer->render('VehicleDetails', [
      'data' => $formattedData,
      'keysNoValueFromApi' => $keysNoValueFromApi,
      'keysNoMapping' => $keysNoMapping
    ]);
    $this->response->setContent($html);
    return $this->response;
  }

  private function formatData(array $data): array
  {
    $keyMapping = [
      'registrationNumber' => 'Registration Number',
      'taxStatus' => 'Tax Status',
      'taxDueDate' => 'Tax Due Date',
      'motStatus' => 'MOT Status',
      'motExpiryDate' => 'MOT Expiry Date',
      'yearOfManufacture' => 'Year of Manufacture',
      'monthOfFirstRegistration' => 'Month of 1ˢᵗ Registration',
      'make' => 'Make',
      'colour' => 'Colour',
      'fuelType' => 'Fuel Type',
      'engineCapacity' => 'Engine Capacity',
      'co2Emissions' => 'CO2 Emissions',
      'dateOfLastV5CIssued' => 'Date of Last V5C Issued',
      'wheelplan' => 'Wheel Plan',
      'typeApproval' => 'Type Approval',
      'markedForExport' => 'Marked for Export'
    ];

    $formattedData = [];
    $keysNoMapping = [];
    $keysNoValueFromApi = [];

    // Format data.
    // Fill array of `key => value` pairs from DVLA API that have no mapping.
    foreach ($keyMapping as $key => $formattedKey) {
      // If DVLA API key has valid mapping, use formatted key.
      if (isset($data[$key])) {
        $formattedData[$formattedKey] = $data[$key];
      } else {
        $formattedData[$formattedKey] = null;
        $keysNoMapping[] = $formattedKey;
      }
    }

    // Fill array of `key => null` pairs of formatted keys that have no value
    // from DVLA API.
    foreach ($data as $key => $value) {
      if (!isset($keyMapping[$key])) {
        $formattedData[$key] = $value;
        $keysNoValueFromApi[] = $key;
      }
    }

    return [$formattedData, $keysNoValueFromApi, $keysNoMapping];
  }
}
