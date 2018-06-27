<?php

require 'vendor/autoload.php';
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

date_default_timezone_set('UTC');

try {

  $required_env_vars = [
    'FERMENTRACK_URL',
    'AWS_REGION',
    'DYNAMODB_TABLE'
  ];

  foreach($required_env_vars as $env_var) {
    if (empty(getenv($env_var))) {
      throw(new Exception($env_var . " is not set"));
    }
  }

  // Get the data from the Fermentrack API, extract relevant fields
  // and store in an object $object
  $brewery_api_url = getenv('FERMENTRACK_URL') . '/api/lcd/1';
  $client = new GuzzleHttp\Client();

  $res = $client->request('GET', $brewery_api_url);
  $brewery_data = json_decode($res->getBody());
  $object = new stdClass();
  // ts is the primary key for the table.
  $object->ts = time();
  foreach($brewery_data[0]->lcd_data as $data) {
    $data_fields = preg_split('/\s+/', $data);
    if ($data_fields[0] == 'Mode' || $data_fields[0] == 'Beer' || $data_fields[0] == 'Fridge') {
      $object->{$data_fields[0]} = $data_fields[1];
    }
  }

  // Fire up the SDK and attempt to push the data to the table.
  $sdk = new Aws\Sdk([
    'region'   => getenv('AWS_REGION'),
    'version'  => 'latest'
  ]);

  $dynamodb = $sdk->createDynamoDb();
  $marshaler = new Marshaler();

  $tableName = getenv('DYNAMODB_TABLE');

  $item = $marshaler->marshalJson(json_encode($object));

  $params = [
    'TableName' => $tableName,
    'Item' => $item
  ];


  $result = $dynamodb->putItem($params);
  echo "Added item: " . print_r($object,1) . "\n";

}
catch (DynamoDbException $e)
{
  echo "Unable to add item:\n";
  echo $e->getMessage() . "\n";
}
catch (Exception $e)
{
  echo "An error occurred:\n";
  echo $e->getMessage() . "\n";
}
