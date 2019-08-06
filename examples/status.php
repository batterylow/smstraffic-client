<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SmsTraffic\Client;
use SmsTraffic\Exceptions\SmsTrafficException;

$testUrl = 'http://localhost:8080/test_endpoint.php';

$login = 'test_login';
$password = 'test_passport';

$smsId = '999999999999999999';

$client = new Client($login, $password, $testUrl);

try {
    $response = $client->status($smsId);
} catch (SmsTrafficException $e) {
    echo 'Status error: ' . $e->getMessage();
    exit(1);
}

echo 'Status for message ' . $response->getContents()->sms_id . PHP_EOL
    . '  submition date: ' . $response->getContents()->submition_date . PHP_EOL
    . '  send_date: ' . $response->getContents()->send_date . PHP_EOL
    . '  last_status_change_date: ' . $response->getContents()->last_status_change_date . PHP_EOL
    . '  status: ' . $response->getContents()->status . PHP_EOL
    . '  error: ' . $response->getContents()->error . PHP_EOL;

    echo (string) $response;

exit(0);
